<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\LabSoftware;
use App\Models\Software;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SoftwareController extends Controller
{
    public function softwares()
    {
        $softwares = Software::with('labs')->orderBy('name')->get();
        $labs = Labs::orderBy('name')->get();

        return view('admin.softwares', [
            'title' => 'Software Management',
            'softwares' => $softwares,
            'labs' => $labs,
        ]);
    }

    public function createSoftware(Request $request)
    {
        $data = $request->only('name', 'version', 'description', 'lab_ids');
        $valid = Validator::make(
            $data,
            [
                'name' => 'required|string|max:255',
                'version' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'lab_ids' => 'nullable|array',
                'lab_ids.*' => 'uuid|exists:labs,id',
            ],
            [
                'name.required' => 'Nama Software wajib diisi.',
                'name.string' => 'Nama Software harus berupa string.',
                'name.max' => 'Nama Software maksimal 255 karakter.',
                'version.string' => 'Versi Software harus berupa string.',
                'version.max' => 'Versi Software maksimal 100 karakter.',
                'description.string' => 'Deskripsi Software harus berupa string.',
                'lab_ids.array' => 'Lab IDs harus berupa array.',
                'lab_ids.*.uuid' => 'Setiap Lab ID harus berupa UUID yang valid.',
                'lab_ids.*.exists' => 'Lab dengan ID tersebut tidak ditemukan.',
            ]
        );
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
            $software = Software::create([
                'name' => $data['name'],
                'version' => $data['version'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
            $message = 'Software berhasil dibuat.';
            if (!empty($data['lab_ids'])) {
                foreach ($data['lab_ids'] as $labId) {
                    LabSoftware::create([
                        'lab_id' => $labId,
                        'software_id' => $software->id
                    ]);
                }
                $message = 'Software berhasil dibuat dan dihubungkan ke Lab yang dipilih.';
            }
            DB::commit();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message ?? 'Software berhasil dibuat.',
                    'software' => $software,
                ]);
            }
            return redirect()->back()->with('success', 'Software berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat membuat Software: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat Software: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteSoftware(Request $request, $id)
    {
        $software = Software::find($id);
        if (!$software) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Software tidak ditemukan.',
                ]);
            }
            return redirect()->back()->with('error', 'Software tidak ditemukan.');
        }

        try {
            $message = 'Software ' . $software->name . ' berhasil dihapus.';

            $software->delete();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' =>  $message ?? 'Software berhasil dihapus.',
                ]);
            }
            return redirect()->back()->with('success', $message ?? 'Software berhasil dihapus.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus Software: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus Software: ' . $e->getMessage());
        }
    }

    public function editSoftware(Request $request, $id)
    {
        $data = $request->only('name', 'version', 'description', 'lab_ids');
        $valid = Validator::make(
            $data,
            [
                'name' => 'required|string|max:255',
                'version' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'lab_ids' => 'nullable|array',
                'lab_ids.*' => 'uuid|exists:labs,id',
            ],
            [
                'name.required' => 'Nama Software wajib diisi.',
                'name.string' => 'Nama Software harus berupa string.',
                'name.max' => 'Nama Software maksimal 255 karakter.',
                'version.string' => 'Versi Software harus berupa string.',
                'version.max' => 'Versi Software maksimal 100 karakter.',
                'description.string' => 'Deskripsi Software harus berupa string.',
                'lab_ids.array' => 'Lab IDs harus berupa array.',
                'lab_ids.*.uuid' => 'Setiap Lab ID harus berupa UUID yang valid.',
                'lab_ids.*.exists' => 'Lab dengan ID tersebut tidak ditemukan.',
            ]
        );
        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ]);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }

        $software = Software::lockForUpdate()->find($id);
        if (!$software) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Software tidak ditemukan.',
                ]);
            }
            return redirect()->back()->with('error', 'Software tidak ditemukan.');
        }
        try {
            DB::beginTransaction();
            $software->update([
                'name' => $data['name'],
                'version' => $data['version'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
            if (isset($data['lab_ids'])) {
                $labSoftwareExist = LabSoftware::where('software_id', $software->id)->pluck('lab_id')->toArray();
                $toAttach = array_diff($data['lab_ids'], $labSoftwareExist);
                $toDetach = array_diff($labSoftwareExist, $data['lab_ids']);
                if (!empty($toAttach)) {
                    foreach ($toAttach as $labId) {
                        LabSoftware::create([
                            'lab_id' => $labId,
                            'software_id' => $software->id
                        ]);
                    }
                }
                if (!empty($toDetach)) {
                    LabSoftware::where('software_id', $software->id)
                        ->whereIn('lab_id', $toDetach)
                        ->delete();
                }
            }
            DB::commit();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Software berhasil diperbarui.',
                    'software' => $software,
                ]);
            }
            return redirect()->back()->with('success', 'Software berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui Software: ' . $e->getMessage(),
                ]);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui Software: ' . $e->getMessage())->withInput();
        }
    }
}
