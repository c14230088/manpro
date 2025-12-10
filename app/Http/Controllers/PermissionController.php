<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function permissions()
    {
        $users = User::with(['permissions', 'unit.permissions'])->get();
        $units = Unit::with('permissions')->get();
        $permissions = Permission::with('permission_group')->get();

        return view('admin.permissions', [
            'title' => 'Permissions Control',
            'users' => $users,
            'units' => $units,
            'permissions' => $permissions
        ]);
    }

    public function updateUserPermissions(Request $request)
    // toggle permissions for Models, 1 per request
    {
        $data = $request->only('model_type', 'model_id', 'permission_id');
        $valid = Validator::make(
            $data,
            [
                'model_type' => 'required|string|in:USER,UNIT',
                'model_id' => [
                    'required',
                    'uuid',
                    function ($attribute, $value, $fail) use ($data) {
                        if ($data['model_type'] === 'USER') {
                            if (!User::where('id', $value)->exists()) {
                                $fail('User dengan ID tersebut tidak ditemukan.');
                            }
                        } elseif ($data['model_type'] === 'UNIT') {
                            if (!Unit::where('id', $value)->exists()) {
                                $fail('Unit dengan ID tersebut tidak ditemukan.');
                            }
                        }
                    },
                ],
                'permission_id' => 'required|uuid',
            ],
            [
                'model_id.required' => 'Pilih user yang akan diubah Aksesnya.',
                'model_id.uuid' => 'Invalid ID format.',
                'model_type.required' => 'Pilih tipe antara User atau Unit.',
                'model_type.in' => 'Invalid model type, Pilih antara User atau Unit.',
                'permission_id.required' => 'Pilih permission yang akan diubah.',
                'permission_id.uuid' => 'Invalid Permission ID format.',
            ]
        );
        $permission = Permission::find($data['permission_id']);

        if (!$permission) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission dengan ID tersebut tidak ditemukan.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Permission dengan ID tersebut tidak ditemukan.');
        }

        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }
        $modelType = $data['model_type'] === 'USER' ? User::class : Unit::class;
        $model = $modelType::find($data['model_id']);

        $message = 'User permissions updated successfully.';

        // toggle permission, jika ada hapus, jika tidak ada tambah
        if ($model->permissions->contains('id', $data['permission_id'])) {
            // hapus permission
            $model->permissions()->detach($data['permission_id']);
            $message = "Akses " . strtoupper($model->name) . " terhadap " . strtoupper($permission->name) . " berhasil DIHAPUS.";
        } else {
            // tambah permission
            $model->permissions()->attach($data['permission_id']);
            $message = "Akses " . strtoupper($model->name) . " terhadap " . strtoupper($permission->name) . " berhasil DITAMBAHKAN.";
        }
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }
        return redirect()->back()->with('success', 'Akses berhasil diperbarui.');
    }

    public function roles()
    {
        $users = User::with(['permissions.permission_group', 'unit.permissions.permission_group'])->get();
        $units = Unit::with('permissions.permission_group')->get();
        $permissions = Permission::with('permission_group')->get();

        return view('admin.roles', [
            'title' => 'Roles Control',
            'users' => $users,
            'units' => $units,
            'permissions' => $permissions
        ]);
    }

    public function updateUserRoles(Request $request)
    // toggle Roles for Users HANYA 1 UNIT per USER, 1 per request
    {
        $data = $request->only('unit_id', 'user_id');
        $valid = Validator::make(
            $data,
            [
                'unit_id' => 'required|uuid|exists:units,id',
                'user_id' => 'required|uuid|exists:users,id',
            ],
            [
                'unit_id.required' => 'Pilih unit yang akan diassign ke user.',
                'unit_id.uuid' => 'Invalid Unit ID format.',
                'unit_id.exists' => 'Unit dengan ID tersebut tidak ditemukan.',

                'user_id.required' => 'Pilih user yang akan diassign ke unit.',
                'user_id.uuid' => 'Invalid User ID format.',
                'user_id.exists' => 'User dengan ID tersebut tidak ditemukan.',
            ],
        );

        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }
        $user = User::find($data['user_id']);
        $unit = Unit::find($data['unit_id']);
        
        $message = 'Roles berhasil diperbarui.';
        // toggle role, jika sama maka unassign, jika tidak sama ganti
        if ($user->unit && $user->unit->id === $unit->id) {
            // unassign dari unit
            $user->unit()->dissociate();
            $user->save();
            $message = "Akses " . strtoupper($user->name) . " pada unit " . strtoupper($unit->name) . " berhasil DIHAPUS.";
        } else {
            // assign ke unit
            $user->unit()->associate($unit);
            $user->save();
            $message = "Role " . strtoupper($user->name) . " pada unit " . strtoupper($unit->name) . " berhasil DITAMBAHKAN.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }
        return redirect()->back()->with('success', $message || 'Roles berhasil diperbarui.');
    }

    public function createUnit(Request $request)
    {
        $data = $request->only('name', 'description');
        $valid = Validator::make(
            $data,
            [
                'name' => 'required|string|max:255|unique:units,name',
                'description' => 'nullable|string|max:500',
            ],
            [
                'name.required' => 'Nama unit wajib diisi.',
                'name.max' => 'Nama unit maksimal 255 karakter.',
                'name.unique' => 'Nama unit sudah digunakan.',
                'description.max' => 'Deskripsi maksimal 500 karakter.',
            ]
        );

        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }

        $unit = Unit::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit "' . $unit->name . '" berhasil dibuat.',
                'unit' => $unit,
            ]);
        }

        return redirect()->back()->with('success', 'Unit berhasil dibuat.');
    }
}
