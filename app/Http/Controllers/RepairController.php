<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use App\Models\Repair;
use App\Models\Booking;
use App\Models\Components;
use App\Models\SpecAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RepairController extends Controller
{
    public function viewRepairs()
    {
        $repairs = Repair::with(['reporter', 'itemable' => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                Items::class => ['type', 'specSetValues.specAttributes', 'desk.lab'],
                Components::class => ['type', 'specSetValues.specAttributes', 'item.desk.lab'],
            ]);
        }])
            ->orderBy('reported_at', 'desc')
            ->get();

        $labs = Labs::all();
        $types = Type::all();
        $specification = SpecAttributes::with('specValues')->get();

        return view('admin.repairs', [
            'repairs'       => $repairs,
            'labs'          => $labs,
            'types'         => $types,
            'specification' => $specification
        ]);
    }

    public function applyRepair(Request $request)
    {
        if (!Auth::check()) {
            $message = 'Anda harus login terlebih dahulu untuk melaporkan kerusakan.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 401);
            }
            return redirect()->route('user.login')->with('error', $message);
        }

        $validated = $request->validate([
            'issue_description' => 'required|string',
            'itemable_id'       => 'required|uuid',
            'is_component'      => 'required|boolean',
            'item_component_ids' => 'nullable|array', // Validasi untuk komponen yang terbawa, logic ini memperbolehkan adanya komponen yang tidak terbawa
            'item_component_ids.*' => 'required|uuid|exists:components,id' // Pastikan semua ID ada di tabel components
        ], [
            'is_component.required' => 'Mohon pilih jenis barang (Item atau Component).',
            'is_component.boolean' => 'Pilihan jenis barang hanya ada Item atau Component.',
            'issue_description.required' => 'Deskripsi masalah harus diisi.',
            'item_component_ids.array' => 'Data komponen tidak valid.',
            'item_component_ids.*.uuid' => 'ID Komponen tidak valid.',
            'item_component_ids.*.exists' => 'Salah satu komponen tidak ditemukan.',
        ]);

        $itemableType = $validated['is_component'] ? Components::class : Items::class;
        $reportedTime = now('Asia/Jakarta');
        $reporterId = Auth::id();

        try {
            DB::beginTransaction();

            Repair::create([
                'issue_description' => $validated['issue_description'],
                'itemable_id'       => $validated['itemable_id'],
                'itemable_type'     => $itemableType,
                'reported_by'       => $reporterId,
                'reported_at'       => $reportedTime,
                'status'            => 0,
            ]);

            if ($itemableType === Items::class && !empty($validated['item_component_ids'])) {
                $childRepairData = [];
                foreach ($validated['item_component_ids'] as $componentId) {
                    $childRepairData[] = [
                        'issue_description' => 'Terbawa karena Item induk sedang diperbaiki',
                        'itemable_id'       => $componentId,
                        'itemable_type'     => Components::class,
                        'reported_by'       => $reporterId,
                        'reported_at'       => $reportedTime,
                        'status'            => 3,
                    ];
                }

                if (!empty($childRepairData)) {
                    Repair::insert($childRepairData);
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan perbaikan Anda telah berhasil diajukan.'
                ], 201);
            }

            return redirect()->back()->with('success', 'Laporan perbaikan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan repair: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan laporan.');
        }
    }

    public function updateRepairStatus(Request $request, Repair $repair)
    // jika parent kondisi sukses maka semua child juga sukses, begitu juga sebaliknya
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:0,1,2',
            'is_successful' => 'nullable|boolean', // true = sudah bagus, false = masih rusak (gagal perbaikan)
            'repair_notes' => 'nullable|string|max:255'
        ], [
            'status.in' => 'Status perbaikan tidak valid. Hanya boleh Pending, In Progress, atau Completed.',
            'is_successful.boolean' => 'Status keberhasilan perbaikan tidak valid, hanya Sukses dan Gagal.',
            'repair_notes.string' => 'Catatan perbaikan harus berupa teks.',
            'repair_notes.max' => 'Catatan perbaikan maksimal 255 karakter.'
        ]);

        DB::beginTransaction();
        try {
            $status = (int) $validated['status'];
            if ($status !== $repair->status += 1) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status repair tidak dapat rollback (Mundur kembali ke sebelumnya).',
                    ], 400);
                }
                return redirect()->back()->with('error', 'Status repair tidak dapat rollback (Mundur kembali ke sebelumnya).');
            }

            $repair->status = $status;
            $currentTime = now('Asia/Jakarta');

            if ($status === 1) { // 1: In Progress
                if (!$repair->started_at) {
                    $repair->started_at = $currentTime;
                }
            } elseif ($status === 2) { // 2: Completed
                $repair->completed_at = $currentTime;
                if (!$repair->started_at) {
                    $repair->started_at = $currentTime;
                }
                if (!isset($validated['is_successful'])) {
                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Status keberhasilan (Sukses atau Gagal) perbaikan harus diisi saat menyelesaikan perbaikan.',
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Status keberhasilan (Sukses atau Gagal) perbaikan harus diisi saat menyelesaikan perbaikan.');
                }

                $repair->is_successful = $validated['is_successful'];
                $repair->repair_notes = $validated['repair_notes'] ?? '-';
            }
            $repair->save(); // item parent repair

            // Cek jika update status repair berupa Item
            if ($repair->itemable_type == Items::class) {

                $item = $repair->itemable()->with('components')->first();

                if ($item && $item->components->isNotEmpty()) {
                    $componentIds = $item->components->pluck('id');

                    // Cari component repair yang berstatus 3 (terbawa)
                    $childRepairs = Repair::where('itemable_type', Components::class)
                        ->whereIn('itemable_id', $componentIds)
                        ->where('status', 3) // Hanya update yang statusnya 'Terbawa'
                        ->whereNull('completed_at') // dan belum selesai
                        ->get();

                    foreach ($childRepairs as $childRepair) {
                        if ($status === 1) { // 1: In Progress
                            // Update timestamp saja, status tetap 3
                            $childRepair->started_at = $repair->started_at;
                        } elseif ($status === 2) { // 2: Completed
                            // Update status dan timestamp
                            $childRepair->status = 2; // Ikut Selesai
                            $childRepair->started_at = $repair->started_at;
                            $childRepair->completed_at = $repair->completed_at;

                            $childRepair->is_successful = $repair->is_successful;
                            $childRepair->repair_notes = 'Catatan Reparasi Item Induk: ' . $repair->repair_notes;
                        }
                        $childRepair->save();
                    }
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status perbaikan berhasil diperbarui.',
                'data'    => $repair
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui status repair: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
