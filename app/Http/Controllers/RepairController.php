<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use App\Models\Repair;
use App\Models\Booking;
use App\Models\Components;
use App\Models\Repairs_item;
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
        $repairs = Repair::with([
            'reporter',

            'items.type',
            'items.desk.lab',
            'items.specSetValues.specAttributes',

            'components.type',
            'components.item.desk.lab',
            'components.specSetValues.specAttributes'
        ])
            ->orderBy('created_at', 'desc')
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
            'items'                     => 'nullable|array',
            'items.*.id'                => 'required|uuid|exists:items,id',
            'items.*.issue_description' => 'required|string',

            'components'                     => 'nullable|array',
            'components.*.id'                => 'required|uuid|exists:components,id',
            'components.*.issue_description' => 'required|string',
        ], [
            'items.array'                   => 'Format data item tidak valid.',
            'items.*.id.exists'             => 'Salah satu item tidak ditemukan di database.',
            'items.*.issue_description.required' => 'Deskripsi masalah item wajib diisi.',
            'components.array'              => 'Format data komponen tidak valid.',
            'components.*.id.exists'        => 'Salah satu komponen tidak ditemukan di database.',
            'components.*.issue_description.required' => 'Deskripsi masalah komponen wajib diisi.',
        ]);

        if (empty($validated['items']) && empty($validated['components'])) {
            $msg = 'Mohon pilih minimal satu Item atau Component untuk diperbaiki.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $reportedTime = now('Asia/Jakarta');
        $reporter = Auth::user();

        try {
            DB::beginTransaction();

            $countItems = !empty($validated['items']) ? count($validated['items']) : 0;
            $countComps = !empty($validated['components']) ? count($validated['components']) : 0;
            $totalCount = $countItems + $countComps;

            $repair = Repair::create([
                'name'        => $reporter->name . '-' . $reportedTime->format('YmdHis') . '-BATCH-' . $totalCount,
                'reported_by' => $reporter->id,
            ]);

            if (!empty($validated['items'])) {
                $itemAttachments = [];
                foreach ($validated['items'] as $itemData) {
                    $itemAttachments[$itemData['id']] = [
                        'issue_description' => $itemData['issue_description'],
                        'reported_at'       => $reportedTime,
                        'status'            => 0,
                    ];
                }
                $repair->items()->attach($itemAttachments);
            }

            if (!empty($validated['components'])) {
                $compAttachments = [];
                foreach ($validated['components'] as $compData) {
                    $compAttachments[$compData['id']] = [
                        'issue_description' => $compData['issue_description'], // Deskripsi spesifik
                        'reported_at'       => $reportedTime,
                        'status'            => 0,
                    ];
                }
                $repair->components()->attach($compAttachments);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan perbaikan berhasil diajukan.'
                ], 201);
            }

            return redirect()->back()->with('success', 'Laporan perbaikan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan repair specific: ' . $e->getMessage());

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
    {
        $isPartialUpdate = $request->has('items') && is_array($request->items);

        $rules = [
            'status'        => $isPartialUpdate ? 'nullable' : 'required|integer|in:0,1,2',
            'is_successful' => 'nullable|boolean',
            'repair_notes'  => 'nullable|string|max:255',

            'items'                  => 'nullable|array',
            'items.*.itemable_id'    => 'required|uuid',
            'items.*.status'         => 'nullable|integer|in:0,1,2',
            'items.*.is_successful'  => 'nullable|boolean',
            'items.*.repair_notes'   => 'nullable|string',
        ];

        $validated = $request->validate($rules);
        $currentTime = now('Asia/Jakarta');
        // Log::info($validated);
        DB::beginTransaction();
        try {
            if ($isPartialUpdate) {
                foreach ($validated['items'] as $itemData) {
                    $pivotQuery = Repairs_item::where('repair_id', $repair->id)
                        ->where('itemable_id', $itemData['itemable_id']);

                    $currentItem = $pivotQuery->first();

                    if (!$currentItem) {
                        // throw new \Exception("Id barang dan Repair tidak sesuai, tidak ada barang tersebut dalam Reparasi ini.");
                        continue;
                    };

                    $updateData = [];

                    if (isset($itemData['status'])) {
                        $newStatus = (int)$itemData['status'];

                        if ($newStatus !== $currentItem->status && $newStatus !== ($currentItem->status + 1)) {
                            $msg = "Status untuk item {$itemData['itemable_id']} tidak urut atau mencoba rollback.";
                            if ($request->wantsJson()) {
                                return response()->json(['message' => $msg], 400);
                            }
                            return redirect()->back()->with('error', $msg);
                        }

                        $updateData['status'] = $newStatus;

                        if ($newStatus === 1) {
                            if (!$currentItem->started_at) {
                                $updateData['started_at'] = $currentTime;
                            }
                        } elseif ($newStatus === 2) {
                            $updateData['completed_at'] = $currentTime;
                            if (!$currentItem->started_at) {
                                $updateData['started_at'] = $currentTime;
                            }
                        }
                    }

                    if (isset($itemData['repair_notes'])) {
                        $updateData['repair_notes'] = $itemData['repair_notes'];
                    }
                    if (isset($itemData['is_successful'])) {
                        $updateData['is_successful'] = $itemData['is_successful'];
                    }

                    if (!empty($updateData)) {
                        $pivotQuery->update($updateData);
                    }
                }
            } else {
                $status = (int) $validated['status'];

                $pivotUpdateData = [
                    'status' => $status,
                ];

                if (isset($validated['is_successful'])) {
                    $pivotUpdateData['is_successful'] = $validated['is_successful'];
                }
                if (isset($validated['repair_notes'])) {
                    $pivotUpdateData['repair_notes'] = $validated['repair_notes'];
                }

                if ($status === 1) {
                    Repairs_item::where('repair_id', $repair->id)
                        ->whereNull('started_at')
                        ->update(['started_at' => $currentTime]);
                } elseif ($status === 2) {
                    $pivotUpdateData['completed_at'] = $currentTime;

                    Repairs_item::where('repair_id', $repair->id)
                        ->whereNull('started_at')
                        ->update(['started_at' => $currentTime]);
                }

                Repairs_item::where('repair_id', $repair->id)->update($pivotUpdateData);

                if ($request->has('items') && is_array($request->items)) {
                    foreach ($validated['items'] as $specificNote) {
                        if (isset($specificNote['repair_notes']) && isset($specificNote['itemable_id'])) {
                            Repairs_item::where('repair_id', $repair->id)
                                ->where('itemable_id', $specificNote['itemable_id'])
                                ->update(['repair_notes' => $specificNote['repair_notes']]);
                        }
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status perbaikan berhasil diperbarui.',
                    'data'    => $repair->load('repairs_item')
                ], 200);
            }

            return redirect()->back()->with('success', 'Status perbaikan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update status repair: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server.');
        }
    }
}
