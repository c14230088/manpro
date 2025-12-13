<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Items;
use App\Models\Period;
use App\Models\Booking;
use App\Models\Components;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function formBooking()
    {
        return view('user.booking-new', ['title' => 'User | Formulir Peminjaman']);
    }

    public function getMyBookings()
    {
        $userId = Auth::user()->id;
        $bookings = Booking::with(['supervisor', 'approver', 'period', 'bookings_items.bookable', 'bookings_items.returner'])
            ->where('borrower_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.booking-history', [
            'title' => 'Riwayat Peminjaman Saya',
            'bookings' => $bookings
        ]);
    }

    public function getBookingDetails($id)
    {
        $booking = Booking::with([
            'borrower.unit',
            'supervisor.unit',
            'approver.unit',
            'period',
            'bookings_items.bookable' => function ($morphTo) {
                $morphTo->morphWith([
                    Items::class => ['type', 'desk.lab', 'lab', 'specSetValues.specAttributes', 'components.type', 'components.specSetValues.specAttribute'],
                    Components::class => ['type', 'item.desk.lab', 'lab', 'specSetValues.specAttributes'],
                    Labs::class => []
                ]);
            }
        ])->find($id);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman yang dipilih tidak ditemukan.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    public function storeBooking(Request $request)
    {
        $bookingType = $request->input('booking_type');

        if ($bookingType === 'sets') {
            return $this->storeSetBooking($request);
        } elseif ($bookingType === 'items') {
            return $this->storeItemsBooking($request);
        }

        $period = $this->validatePeriod();
        if (!$period['success']) return $period['response'];

        $data = $request->only([
            'bookable_type',
            'bookable_id',
            'event_name',
            'event_started_at',
            'event_ended_at',
            'thesis_title',
            'supervisor_id',
            'attendee_count',
            'phone_number',
            'booking_detail',
            'borrowed_at',
            'return_deadline_at',
            'type',
        ]);
        $data['borrower_id'] = Auth::user()->id;
        $data['period_id'] = $period['data']->id;

        $valid = Validator::make($data, [
            'bookable_type' => 'required|in:item,component,lab',
            'bookable_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) use ($data) {
                    $type = $data['bookable_type'] . 's';
                    if (!DB::table(strtolower($type))->where('id', $value)->exists()) {
                        return $fail(ucfirst($type) . ' yang dipilih tidak ditemukan.');
                    }
                    if ($this->isBookableConflict('App\\Models\\' . ucfirst($type), $value, $data['borrowed_at'], $data['return_deadline_at'])) {
                        $fail('Maaf, ' . ucfirst($type) . ' ini sudah dibooking/sedang dipinjam pada rentang waktu tersebut.');
                    }
                },
            ],
            'event_name' => 'required|string|max:255',
            'event_started_at' => 'required|date|after:now',
            'event_ended_at' => 'required|date|after:event_started_at',
            'thesis_title' => 'nullable|string|max:255',
            'supervisor_id' => 'nullable|uuid|exists:users,id',
            'attendee_count' => 'nullable|integer|min:1',
            'phone_number' => 'required|string|regex:/^\d{10,20}$/',
            'booking_detail' => 'nullable|string|max:750',
            'borrowed_at' => 'required|date|before:event_ended_at',
            'return_deadline_at' => 'required|date|after:borrowed_at',
            'type' => 'required|in:0,1,2',
        ], $this->getValidationMessages());

        if ($valid->fails()) {
            return response()->json(['success' => false, 'message' => $valid->errors()->first()]);
        }

        try {
            DB::beginTransaction();
            $booking = Booking::create($data);

            if ($data['bookable_type'] === 'lab') {
                $lab = Labs::find($data['bookable_id']);
                if ($lab->capacity < $data['attendee_count']) {
                    throw new \Exception('Kapasitas ' . $lab->name . ' tidak mencukupi untuk jumlah peserta yang diinginkan. Maksimal: ' . $lab->capacity . ' Orang.');
                }
            }

            $booking->bookings_items()->create([
                'bookable_type' => 'App\\Models\\' . ucfirst($data['bookable_type'] . 's'),
                'bookable_id' => $data['bookable_id'],
                'type' => $data['type']
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permintaan peminjaman berhasil diajukan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage() ?? 'Terjadi kesalahan saat mengajukan permintaan peminjaman. Silakan coba lagi.']);
        }
    }

    private function storeSetBooking(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'sets' => 'required|array|min:1',
            'sets.*.lab_id' => 'required|uuid|exists:labs,id',
            'sets.*.quantity' => 'required|integer|min:1',

            'event_name' => 'required|string|max:255',
            'event_started_at' => 'required|date|after:now',
            'event_ended_at' => 'required|date|after:event_started_at',

            'phone_number' => 'required|string',
            'borrowed_at' => 'required|date',
            'return_deadline_at' => 'required|date|after:borrowed_at',

            'type' => 'required|in:0,1,2',
        ], [

            // --- SETS ---
            'sets.required' => 'Minimal satu set harus dipilih.',
            'sets.array' => 'Format set tidak valid.',
            'sets.min' => 'Minimal harus memilih satu set.',

            'sets.*.lab_id.required' => 'Lab untuk setiap set harus diisi.',
            'sets.*.lab_id.uuid' => 'ID lab tidak valid.',
            'sets.*.lab_id.exists' => 'Lab yang dipilih tidak ditemukan.',

            'sets.*.quantity.required' => 'Jumlah set harus diisi.',
            'sets.*.quantity.integer' => 'Jumlah set harus berupa angka.',
            'sets.*.quantity.min' => 'Jumlah set tidak boleh kurang dari 1.',

            // --- EVENT INFO ---
            'event_name.required' => 'Nama event wajib diisi.',
            'event_name.string' => 'Nama event tidak valid.',
            'event_name.max' => 'Nama event tidak boleh lebih dari 255 karakter.',

            'event_started_at.required' => 'Waktu mulai event wajib diisi.',
            'event_started_at.date' => 'Format waktu mulai event tidak valid.',
            'event_started_at.after' => 'Waktu mulai event harus lebih dari waktu sekarang.',

            'event_ended_at.required' => 'Waktu selesai event wajib diisi.',
            'event_ended_at.date' => 'Format waktu selesai event tidak valid.',
            'event_ended_at.after' => 'Waktu selesai event harus setelah waktu mulai.',

            // --- PHONE ---
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.string' => 'Format nomor telepon tidak valid.',

            // --- BORROW DETAIL ---
            'borrowed_at.required' => 'Tanggal peminjaman wajib diisi.',
            'borrowed_at.date' => 'Format tanggal peminjaman tidak valid.',

            'return_deadline_at.required' => 'Deadline pengembalian wajib diisi.',
            'return_deadline_at.date' => 'Format deadline pengembalian tidak valid.',
            'return_deadline_at.after' => 'Deadline pengembalian harus setelah tanggal peminjaman.',

            // --- TYPE ---
            'type.required' => 'Tipe peminjaman wajib dipilih.',
            'type.in' => 'Tipe peminjaman tidak valid.',
        ]);


        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ]);
        }

        $period = $this->validatePeriod();
        if (!$period['success']) return $period['response'];

        DB::beginTransaction();
        try {
            $booking = $this->createBooking($request, $period['data']->id);

            $requiredTypes = ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU'];

            foreach ($request->sets as $setRequest) {
                $lab = Labs::find($setRequest['lab_id']);
                $availableSets = [];

                // Check desks
                $desks = $lab->desks()->with(['items.type', 'items.repairs'])->get();
                foreach ($desks as $desk) {
                    $items = $desk->items;
                    if ($items->count() < 4) continue;

                    $typeNames = $items->pluck('type.name')->toArray();
                    if (count(array_intersect($requiredTypes, $typeNames)) !== 4) continue;

                    if (!$items->every(fn($i) => $i->condition == 1)) continue;
                    if ($items->some(fn($i) => $i->repairs->isNotEmpty())) continue;

                    $hasConflict = $items->some(fn($item) => $this->isBookableConflict('App\\Models\\Items', $item->id, $request->borrowed_at, $request->return_deadline_at, true));

                    if (!$hasConflict) {
                        $availableSets[] = ['type' => 'desk', 'items' => $items];
                    }

                    if (count($availableSets) >= $setRequest['quantity']) break;
                }

                // Check lab storage if not enough
                if (count($availableSets) < $setRequest['quantity']) {
                    $storageItems = $lab->items()->with(['type', 'repairs'])
                        ->whereIn('type_id', function ($q) use ($requiredTypes) {
                            $q->select('id')->from('types')->whereIn('name', $requiredTypes);
                        })
                        ->where('condition', 1)
                        ->whereDoesntHave('repairs')
                        ->get();

                    $grouped = $storageItems->groupBy('type.name');
                    $typeCounts = [];

                    foreach ($requiredTypes as $type) {
                        $availableCount = 0;
                        if (isset($grouped[$type])) {
                            foreach ($grouped[$type] as $item) {
                                if (!$this->isBookableConflict('App\\Models\\Items', $item->id, $request->borrowed_at, $request->return_deadline_at, true)) {
                                    $availableCount++;
                                }
                            }
                        }
                        $typeCounts[$type] = $availableCount;
                    }

                    $maxStorageSets = empty($typeCounts) ? 0 : min($typeCounts);
                    $needed = min($maxStorageSets, $setRequest['quantity'] - count($availableSets));

                    $usedIds = [];
                    for ($i = 0; $i < $needed; $i++) {
                        $setItems = collect();
                        foreach ($requiredTypes as $type) {
                            foreach ($grouped[$type] as $item) {
                                if (!in_array($item->id, $usedIds) && !$this->isBookableConflict('App\\Models\\Items', $item->id, $request->borrowed_at, $request->return_deadline_at, true)) {
                                    $setItems->push($item);
                                    $usedIds[] = $item->id;
                                    break;
                                }
                            }
                        }
                        if ($setItems->count() === 4) {
                            $availableSets[] = ['type' => 'storage', 'items' => $setItems];
                        }
                    }
                }

                if (count($availableSets) < $setRequest['quantity']) {
                    throw new \Exception("{$lab->name} tidak memiliki {$setRequest['quantity']} set PC Lengkap. Tersedia: " . count($availableSets) . " set.");
                }

                foreach (array_slice($availableSets, 0, $setRequest['quantity']) as $set) {
                    foreach ($set['items'] as $item) {
                        $booking->bookings_items()->create([
                            'bookable_type' => 'App\\Models\\Items',
                            'bookable_id' => $item->id,
                            'type' => $request->type
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Booking set berhasil diajukan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function storeItemsBooking(Request $request)
    {
        $request->validate([
            'items' => 'nullable|array',
            'items.*' => 'required|uuid|exists:items,id',
            'components' => 'nullable|array',
            'components.*' => 'required|uuid|exists:components,id',
            'event_name' => 'required|string|max:255',
            'event_started_at' => 'required|date|after:now',
            'event_ended_at' => 'required|date|after:event_started_at',
            'phone_number' => 'required|string',
            'borrowed_at' => 'required|date',
            'return_deadline_at' => 'required|date|after:borrowed_at',
            'type' => 'required|in:0,1,2',
        ]);

        if (empty($request->items) && empty($request->components)) {
            return response()->json(['success' => false, 'message' => 'Minimal harus ada 1 item atau component yang dipilih.'], 422);
        }

        $period = $this->validatePeriod();
        if (!$period['success']) return $period['response'];

        DB::beginTransaction();
        try {
            $booking = $this->createBooking($request, $period['data']->id);

            if (!empty($request->items)) {
                foreach ($request->items as $itemId) {
                    $item = Items::find($itemId);

                    if ($item->condition != 1) {
                        throw new \Exception("Item {$item->name} tidak dalam kondisi baik.");
                    }

                    if ($item->repairs->isNotEmpty()) {
                        throw new \Exception("Item {$item->name} sedang dalam perbaikan.");
                    }

                    if ($this->isBookableConflict('App\\Models\\Items', $itemId, $request->borrowed_at, $request->return_deadline_at, true)) {
                        throw new \Exception("Item {$item->name} sudah dibooking pada waktu tersebut.");
                    }

                    $booking->bookings_items()->create([
                        'bookable_type' => 'App\\Models\\Items',
                        'bookable_id' => $itemId,
                        'type' => $request->type
                    ]);
                }
            }

            if (!empty($request->components)) {
                foreach ($request->components as $componentId) {
                    $component = \App\Models\Components::find($componentId);

                    if ($component->condition != 1) {
                        throw new \Exception("Component {$component->name} tidak dalam kondisi baik.");
                    }

                    if ($this->isBookableConflict('App\\Models\\Components', $componentId, $request->borrowed_at, $request->return_deadline_at, true)) {
                        throw new \Exception("Component {$component->name} sudah dibooking pada waktu tersebut.");
                    }

                    $booking->bookings_items()->create([
                        'bookable_type' => 'App\\Models\\Components',
                        'bookable_id' => $componentId,
                        'type' => $request->type
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Booking items berhasil diajukan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateReturnDeadline(Request $request, $id)
    {
        $data = $request->only(['return_deadline_at']);
        $valid = Validator::make($data, [
            'return_deadline_at' => 'required|date|after:borrowed_at',
        ], [
            'return_deadline_at.required' => 'Batas waktu pengembalian harus diisi.',
            'return_deadline_at.date' => 'Batas waktu pengembalian tidak valid.',
            'return_deadline_at.after' => 'Batas waktu pengembalian harus lebih besar dari waktu mengambil peminjaman.',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ], 422);
        }

        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman yang dipilih tidak ditemukan.',
            ], 404);
        }
        $booking->return_deadline_at = $data['return_deadline_at'];
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Batas waktu pengembalian berhasil diperbarui.',
        ]);
    }

    public function approveBooking(Request $request, $id)
    {
        $data = $request->only(['approved']);
        $valid = Validator::make($data, [
            'approved' => 'required|boolean',
        ], [
            'approved.required' => 'Status persetujuan harus diisi.',
            'approved.boolean' => 'Status persetujuan tidak valid, silahkan pilih Setujui atau Tolak.',
        ]);
        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ], 422);
        }

        $booking = Booking::lockForUpdate()->find($id);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman yang dipilih tidak ditemukan.',
            ], 404);
        }
        if (!is_null($booking->approved)) {
            return response()->json([
                'success' => false,
                'message' => 'Persetujuan Peminjaman ini sudah diproses sebelumnya, dan peminjaman ini DI' . ($booking->approved ? 'SETUJUI' : 'TOLAK') . '.',
            ], 400);
        }
        $booking->approved = $data['approved'];
        $booking->approved_at = now();
        $booking->approved_by = Auth::user()->id;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman ini berhasil DI' . ($booking->approved ? 'SETUJUI' : 'TOLAK') . '.',
        ]);
    }

    public function returnBooking(Request $request, $id)
    {
        // dapat menerima 1 atau lebih bookings_items yang dikembalikan
        // dan setiap bookings_items dapat memiliki returned_status dan returned_detail yang sama maupun berbeda
        $data = $request->only(['items', 'global_returned_detail', 'global_returned_status']);
        $valid = Validator::make($data, [
            'items' => 'required|array|min:1',
            'items.*.booking_item_id' => 'required|uuid|exists:bookings_items,id',
            'items.*.returned_status' => isset($data['global_returned_status']) ? 'nullable|boolean' : 'required|boolean',
            'items.*.returned_detail' => 'nullable|string|max:750',

            'global_returned_detail' => 'nullable|string|max:750',  
            'global_returned_status' => 'nullable|boolean',  
        ], [
            'items.required' => 'Daftar item yang dikembalikan harus dipilih minimal 1 barang.',
            'items.array' => 'Format daftar item yang dikembalikan tidak valid.',
            'items.min' => 'Minimal harus ada 1 item yang dikembalikan.',
            'items.*.booking_item_id.required' => 'Barang atau Lab yang dipinjam harus dipilih.',
            'items.*.booking_item_id.uuid' => 'ID Barang atau Lab yang dipinjam tidak valid.',
            'items.*.booking_item_id.exists' => 'Barang atau Lab yang dipinjam yang dipilih tidak ditemukan.',
            'items.*.returned_status.required' => 'Status Kondisi Barang atau Lab yang dikembalikan harus diisi.',
            'items.*.returned_status.boolean' => 'Status Kondisi Barang atau Lab yang dikembalikan tidak valid, harus berupa Baik atau Rusak.',
            'items.*.returned_detail.max' => 'Detail pengembalian maksimal 750 karakter.',
            
            'global_returned_detail.max' => 'Detail pengembalian Global maksimal 750 karakter.',
            'global_returned_status.boolean' => 'Status Kondisi Barang atau Lab Global yang dikembalikan tidak valid, harus berupa Baik atau Rusak.',
        ]);
        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => $valid->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $itemIds = collect($data['items'])->pluck('booking_item_id');

            $bookingItems = DB::table('bookings_items')
                ->whereIn('id', $itemIds)
                ->where('booking_id', $id)
                ->whereNull('returned_at')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($bookingItems->count() !== count($itemIds)) {
                throw new \Exception("Ada barang yang tidak valid / sudah dikembalikan");
            }

            foreach ($data['items'] as $item) {
                DB::table('bookings_items')
                    ->where('id', $item['booking_item_id'])
                    ->update([
                        'returned_at' => now(),
                        'returned_status' => $item['returned_status'] ?? ($data['global_returned_status'] ?? true),
                        'returned_detail' => $item['returned_detail'] ?? ($data['global_returned_detail'] ?? null),
                        'returner_id' => Auth::user()->id,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang atau Lab berhasil dikembalikan'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pengembalian.'
            ], 500);
        }
    }

    public function bookings()
    {
        $bookings = Booking::with([
            'borrower.unit',
            'supervisor.unit',
            'approver.unit',
            'period',
            'bookings_items' => function ($query) {
                $query->with([
                    'bookable' => function ($query) {
                        $query->morphWith([
                            Items::class => ['type'],
                            Components::class => ['type'],
                            Labs::class => []
                        ]);
                    }
                ]);
            }
        ])->orderBy('created_at', 'desc')->get();

        $years = $bookings->pluck('period.academic_year')->unique()->sort()->values();
        $approvers = $bookings->whereNotNull('approver')->pluck('approver')->unique('id')->sortBy('name')->values();
        $units = $bookings->pluck('borrower.unit')->whereNotNull()->unique('id')->sortBy('name')->values();

        return view('admin.bookings', compact('bookings', 'years', 'approvers', 'units'));
    }

    private function validatePeriod()
    {
        $period = Period::getCurrentPeriod();
        if (!$period) {
            return [
                'success' => false,
                'response' => response()->json(['success' => false, 'message' => 'Tidak ada periode yang aktif, Mohon hubungi Admin.'], 404)
            ];
        }
        return ['success' => true, 'data' => $period];
    }

    private function createBooking(Request $request, $periodId)
    {
        return Booking::create([
            'borrower_id' => Auth::user()->id,
            'period_id' => $periodId,
            'event_name' => $request->event_name,
            'event_started_at' => $request->event_started_at,
            'event_ended_at' => $request->event_ended_at,
            'phone_number' => $request->phone_number,
            'borrowed_at' => $request->borrowed_at,
            'return_deadline_at' => $request->return_deadline_at,
            'booking_detail' => $request->booking_detail,
            'thesis_title' => $request->thesis_title,
            'supervisor_id' => $request->supervisor_id,
            'attendee_count' => $request->attendee_count,
        ]);
    }

    private function isBookableConflict($modelType, $bookableId, $borrowedAt, $returnDeadlineAt, $onlyApproved = false)
    {
        $query = DB::table('bookings_items')
            ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
            ->where('bookings_items.bookable_type', $modelType)
            ->where('bookings_items.bookable_id', $bookableId)
            ->where('bookings.borrowed_at', '<', $returnDeadlineAt)
            ->where('bookings.return_deadline_at', '>', $borrowedAt);

        if ($onlyApproved) {
            $query->where('bookings.approved', true);
        } else {
            $query->whereNotIn('bookings.approved', [false, true]);
        }

        return $query->exists();
    }

    private function getValidationMessages()
    {
        return [
            'bookable_type.required' => 'Tipe peminjaman harus diisi.',
            'bookable_type.in' => 'Tipe peminjaman yang dipilih tidak valid, harus berupa Item, Component, atau Lab.',
            'bookable_id.required' => 'Item/Component/Lab yang dipilih harus diisi.',
            'bookable_id.uuid' => 'ID yang dipilih tidak valid.',
            'event_name.required' => 'Nama kegiatan harus diisi.',
            'event_name.max' => 'Nama kegiatan maksimal 255 karakter.',
            'event_started_at.required' => 'Waktu mulai kegiatan harus diisi.',
            'event_started_at.date' => 'Waktu mulai kegiatan tidak valid.',
            'event_started_at.after' => 'Waktu mulai kegiatan harus lebih besar dari waktu sekarang ini.',
            'event_ended_at.required' => 'Waktu selesai kegiatan harus diisi.',
            'event_ended_at.date' => 'Waktu selesai kegiatan tidak valid.',
            'event_ended_at.after' => 'Waktu selesai kegiatan harus lebih besar dari waktu mulai kegiatan.',
            'thesis_title.max' => 'Judul skripsi maksimal 255 karakter.',
            'supervisor_id.uuid' => 'ID dosen pembimbing tidak valid.',
            'supervisor_id.exists' => 'Dosen pembimbing yang dipilih tidak ditemukan.',
            'attendee_count.integer' => 'Jumlah peserta harus berupa angka.',
            'attendee_count.min' => 'Jumlah peserta minimal 1 orang.',
            'phone_number.required' => 'Nomor WhatsApp harus diisi.',
            'phone_number.regex' => 'Nomor WhatsApp tidak valid, harus berupa angka dengan panjang antara 10 hingga 20 digit.',
            'booking_detail.max' => 'Detail peminjaman maksimal 750 karakter.',
            'borrowed_at.required' => 'Waktu mulai peminjaman harus diisi.',
            'borrowed_at.date' => 'Waktu mulai peminjaman tidak valid.',
            'borrowed_at.before' => 'Waktu mengambil peminjaman harus sebelum waktu kegiatan berakhir.',
            'return_deadline_at.required' => 'Batas waktu pengembalian harus diisi.',
            'return_deadline_at.date' => 'Batas waktu pengembalian tidak valid.',
            'return_deadline_at.after' => 'Batas waktu pengembalian harus lebih besar dari waktu mengambil peminjaman.',
            'type.required' => 'Tipe peminjaman harus diisi.',
            'type.in' => 'Tipe peminjaman yang dipilih tidak valid, harus dipilih antara Onsite, Remote, atau Keluar Lab.',
        ];
    }
}
