<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Period;
use App\Models\Booking;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function formBooking()
    {
        return view('user.booking', ['title' => 'User | Form Booking']);
    }

    public function getMyBookings()
    {
        $userId = Auth::user()->id;
        $bookings = Booking::with(['supervisor', 'approver', 'period', 'bookings_items.bookable', 'bookings_items.returner'])
            ->where('borrower_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    public function getBookingDetails($id)
    {
        $booking = Booking::with(['borrower', 'supervisor', 'approver', 'period', 'bookings_items.bookable'])->find($id);
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
        $period = Period::getCurrentPeriod();
        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode yang aktif, Mohon hubungi Admin.',
            ], 404);
        }
        $valid = Validator::make($data, [
            'bookable_type' => 'required|in:item,component,lab',
            'bookable_id' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) use ($data) {
                    $type = $data['bookable_type'] . 's'; // item, component, lab
                    $table = strtolower($type);

                    if (!DB::table($table)->where('id', $value)->exists()) {
                        return $fail(ucfirst($type) . ' yang dipilih tidak ditemukan.');
                    }

                    // 2. Cek apakah Barang/Lab SEDANG DIPINJAM di jam tersebut (Overlap Check)
                    $startRequest = $data['borrowed_at'];
                    $endRequest   = $data['return_deadline_at'];
                    $modelType    = 'App\\Models\\' . ucfirst($type); // Sesuaikan dengan format penyimpanan di DB

                    // Query cek tabrakan jadwal
                    $isBooked = DB::table('bookings_items')
                        ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                        ->where('bookings_items.bookable_type', $modelType)
                        ->where('bookings_items.bookable_id', $value)
                        ->where(function ($query) use ($startRequest, $endRequest) {
                            // Logika Overlap:
                            // (Start Peminjaman Lama < End Request Baru) AND (End Peminjaman Lama > Start Request Baru)
                            $query->where('bookings.borrowed_at', '<', $endRequest)
                                ->where('bookings.return_deadline_at', '>', $startRequest);
                        })
                        // PENTING: Filter status. Jangan hitung booking yang sudah ditolak atau sudah dikembalikan.
                        // Sesuaikan status ini dengan enum/state di aplikasi Anda.
                        ->whereNotIn('bookings.approved', [false, true])
                        ->exists();

                    if ($isBooked) {
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
        ], [
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
        ]);
        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ]);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }
        try {
            DB::beginTransaction();

            $booking = Booking::create($data);
            if ($data['bookable_type'] === 'lab') {
                $lab = Labs::where('id', $data['bookable_id'])->first();
                if ($lab->capacity < $data['attendee_count']) {
                    throw new \Exception('Kapasitas lab tidak mencukupi untuk jumlah peserta yang diinginkan.');
                }
            }
            // Simpan ke tabel pivot bookings_items
            $booking->bookings_items()->create([
                'bookable_type' => 'App\\Models\\' . ucfirst($data['bookable_type'] . 's'),
                'bookable_id' => $data['bookable_id'],
                'type' => $data['type']
            ]);

            DB::commit();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan peminjaman berhasil diajukan.',
                ]);
            }
            return redirect()->route('user.booking.form')->with('success', 'Permintaan peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengajukan permintaan peminjaman. Silakan coba lagi.',
                ]);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengajukan permintaan peminjaman. Silakan coba lagi.')->withInput();
        }
    }

    private function storeSetBooking(Request $request)
    {
        $request->validate([
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
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'borrower_id' => Auth::user()->id,
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

            $requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];

            foreach ($request->sets as $setRequest) {
                $lab = Labs::find($setRequest['lab_id']);
                $desks = $lab->desks()->with(['items.type', 'items.repairs'])->get();

                $availableDesks = [];
                foreach ($desks as $desk) {
                    $items = $desk->items;
                    if ($items->count() < 4) continue;

                    $typeNames = $items->pluck('type.name')->toArray();
                    if (count(array_intersect($requiredTypes, $typeNames)) !== 4) continue;

                    if (!$items->every(fn($i) => $i->condition == 1)) continue;
                    if ($items->some(fn($i) => $i->repairs->isNotEmpty())) continue;

                    $hasConflict = $items->some(function ($item) use ($request) {
                        return DB::table('bookings_items')
                            ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                            ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                            ->where('bookings_items.bookable_id', $item->id)
                            ->where('bookings.borrowed_at', '<', $request->return_deadline_at)
                            ->where('bookings.return_deadline_at', '>', $request->borrowed_at)
                            ->where('bookings.approved', true)
                            ->exists();
                    });

                    if (!$hasConflict) {
                        $availableDesks[] = $desk;
                    }

                    if (count($availableDesks) >= $setRequest['quantity']) break;
                }

                if (count($availableDesks) < $setRequest['quantity']) {
                    throw new \Exception("Lab {$lab->name} tidak memiliki {$setRequest['quantity']} set lengkap yang tersedia.");
                }

                foreach (array_slice($availableDesks, 0, $setRequest['quantity']) as $desk) {
                    foreach ($desk->items as $item) {
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
            'items' => 'required|array|min:1',
            'items.*' => 'required|uuid|exists:items,id',
            'event_name' => 'required|string|max:255',
            'event_started_at' => 'required|date|after:now',
            'event_ended_at' => 'required|date|after:event_started_at',
            'phone_number' => 'required|string',
            'borrowed_at' => 'required|date',
            'return_deadline_at' => 'required|date|after:borrowed_at',
            'type' => 'required|in:0,1,2',
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'borrower_id' => Auth::user()->id,
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

            foreach ($request->items as $itemId) {
                $item = Items::find($itemId);

                if ($item->condition != 1) {
                    throw new \Exception("Item {$item->name} tidak dalam kondisi baik.");
                }

                if ($item->repairs->isNotEmpty()) {
                    throw new \Exception("Item {$item->name} sedang dalam perbaikan.");
                }

                $isBooked = DB::table('bookings_items')
                    ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                    ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                    ->where('bookings_items.bookable_id', $itemId)
                    ->where('bookings.borrowed_at', '<', $request->return_deadline_at)
                    ->where('bookings.return_deadline_at', '>', $request->borrowed_at)
                    ->where('bookings.approved', true)
                    ->exists();

                if ($isBooked) {
                    throw new \Exception("Item {$item->name} sudah dibooking pada waktu tersebut.");
                }

                $booking->bookings_items()->create([
                    'bookable_type' => 'App\\Models\\Items',
                    'bookable_id' => $itemId,
                    'type' => $request->type
                ]);
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
            'items.*.returned_status' => 'required|boolean',
            'items.*.returned_detail' => 'nullable|string|max:750',
        ], [
            'items.required' => 'Daftar item yang dikembalikan harus diisi.',
            'items.array' => 'Format daftar item yang dikembalikan tidak valid.',
            'items.min' => 'Minimal harus ada 1 item yang dikembalikan.',
            'items.*.booking_item_id.required' => 'Barang atau Lab yang dipinjam harus dipilih.',
            'items.*.booking_item_id.uuid' => 'ID Barang atau Lab yang dipinjam tidak valid.',
            'items.*.booking_item_id.exists' => 'Barang atau Lab yang dipinjam yang dipilih tidak ditemukan.',
            'items.*.returned_status.required' => 'Status Kondisi Barang atau Lab yang dikembalikan harus diisi.',
            'items.*.returned_status.boolean' => 'Status Kondisi Barang atau Lab yang dikembalikan tidak valid, harus berupa Bagus atau Rusak.',
            'items.*.returned_detail.max' => 'Detail pengembalian maksimal 750 karakter.',
        ]);
        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($valid)->withInput();
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
                        'returned_status' => $item['returned_status'] ?? $data['global_returned_status'],
                        'returned_detail' => $item['returned_detail'] ?? $data['global_returned_detail'],
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
        $bookings = Booking::with(['borrower', 'supervisor', 'approver', 'period', 'bookings_items.bookable', 'bookings_items.returner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bookings', compact('bookings'));
    }
}
