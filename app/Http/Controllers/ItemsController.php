<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Desks;
use App\Models\Components;
use Illuminate\Support\Str;
use App\Models\SpecSetValue;
use App\Models\Items;
use App\Models\Labs;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;
use function Pest\Laravel\json;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ItemsController extends Controller
{
    public function getItems()
    {
        $items = Items::all();
        return response()->json($items);
    }

    public function getItemsByLab(Labs $lab)
    {
        // Mengambil item yang belum dipinjam, dikelompokkan berdasarkan tipenya, dan dihitung jumlahnya.
        $available_items = Items::where('lab_id', $lab->id)
            ->select('type', DB::raw('count(*) as available_count'))
            ->groupBy('type')
            ->get();

        return response()->json($available_items);
    }
    public function getItemDetails(Items $item)
    {
        $item->load([
            'type',
            'desk.lab',
            'specSetValues.specAttributes',
            'components.type',
            'components.specSetValues.specAttributes',
        ]);

        return response()->json($item);
    }
    public function createItems(Request $request)
    {
        $data = $request->only(['is_component', 'name', 'serial_code', 'condition', 'type', 'specifications']);
        $rules = [
            'is_component' => 'required|boolean',
            'name' => 'required|string|max:255',
            'serial_code' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $existsInItems = Items::where('serial_code', $value)->exists();
                    $existsInComponents = Components::where('serial_code', $value)->exists();

                    if ($existsInItems) $fail("Serial code {$attribute} sudah terdaftar sebagai Item.");
                    if ($existsInComponents) $fail("Serial code {$attribute} sudah terdaftar sebagai Component.");
                },
            ],
            'condition' => 'required|boolean',
            'type' => 'required', // Bisa UUID atau string dengan prefix 'new::' kalau type baru

            'specifications' => 'nullable|array',
            'specifications.*.attribute' => 'required|max:255',
            'specifications.*.value' => 'required|max:255',

        ];

        if ($data['is_component'] == '0') { // berupa Item
            $rules['new_components'] = 'nullable|array';
            $rules['new_components.*.name'] = 'required|string|max:255';
            $rules['new_components.*.serial_code'] = [
                'required',
                'string',
                'max:255',
                'distinct', // serial code unik di dalam array
                // Cek keunikan di DB
                function ($attribute, $value, $fail) {
                    // $attribute akan menjadi "new_components.0.serial_code", kita cek $value
                    $existsInItems = Items::where('serial_code', $value)->exists();
                    $existsInComponents = Components::where('serial_code', $value)->exists();

                    if ($existsInItems) $fail("Serial code komponen {$value} sudah terdaftar sebagai Item.");
                    if ($existsInComponents) $fail("Serial code komponen {$value} sudah terdaftar sebagai Component.");
                },
            ];
            $rules['new_components.*.condition'] = 'required|boolean';
            $rules['new_components.*.type'] = 'required';
            $rules['new_components.*.specifications'] = 'nullable|array';
            $rules['new_components.*.specifications.*.attribute'] = 'required|max:255';
            $rules['new_components.*.specifications.*.value'] = 'required|max:255';
        }

        $valid = Validator::make($data, $rules, [
            'is_component.required' => 'Mohon pilih jenis barang (Item atau Component).',
            'is_component.boolean' => 'Pilihan jenis barang hanya ada Item atau Component.',

            'name.required' => 'Mohon lengkapi Nama barang.',
            'name.string' => 'Nama barang harus berupa teks.',
            'name.max' => 'Nama barang tidak boleh lebih dari :max karakter.',

            'serial_code.required' => 'Mohon lengkapi Serial code barang.',
            'serial_code.string' => 'Serial code harus berupa teks.',
            'serial_code.max' => 'Serial code tidak boleh lebih dari :max karakter.',

            'condition.required' => 'Mohon tentukan kondisi barang.',
            'condition.boolean' => 'Kondisi barang hanya boleh berupa Rusak atau Bagus.',

            'type.required' => 'Mohon pilih tipe atau buat tipe baru untuk barang ini.',

            'specifications.array' => 'Format spesifikasi tidak valid.',

            'specifications.*.attribute.required' => 'Setiap baris spesifikasi harus memiliki Atribut.',
            'specifications.*.attribute.max' => 'Attribute dari Spesifikasi barang tidak boleh lebih dari :max karakter.',

            'specifications.*.value.required' => 'Setiap baris spesifikasi harus memiliki Nilai.',
            'specifications.*.value.max' => 'Value dari Spesifikasi barang tidak boleh lebih dari :max karakter.',

            'new_components.*.name.required' => 'Nama Komponen baru di Item ini tidak boleh kosong.',
            'new_components.*.serial_code.required' => 'Serial Code Komponen baru di Item ini tidak boleh kosong.',
            'new_components.*.serial_code.distinct' => 'Ada Serial Code Komponen baru di Item ini yang duplikat.',
            'new_components.*.condition.required' => 'Kondisi Komponen baru di Item ini harus dipilih.',
            'new_components.*.type.required' => 'Tipe Komponen baru di Item ini harus dipilih.',
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
        $data['name'] = $this->upperWords($data['name'] ?? '');

        try {
            DB::beginTransaction();

            $typeId = $this->getOrCreateType($data['type']);

            $barang = null;

            if ($data['is_component'] == false) {
                $barang = Items::create([
                    'name' => $data['name'],
                    'serial_code' => $data['serial_code'],
                    'condition' => $data['condition'],
                    'type_id' => $typeId,
                ]);

                $newComponentsData = $data['new_components'] ?? [];
                if (!empty($newComponentsData)) {
                    foreach ($newComponentsData as $compData) {
                        $compTypeId = $this->getOrCreateType($compData['type']);

                        $newComp = Components::create([
                            'name' => $this->upperWords($compData['name']),
                            'serial_code' => $compData['serial_code'],
                            'condition' => $compData['condition'],
                            'type_id' => $compTypeId,
                            'item_id' => $barang->id, // Langsung kaitkan ke Item parent
                        ]);

                        // Proses Spesifikasi untuk Komponen (Child)
                        $this->processSpecifications($newComp, $compData['specifications'] ?? []);
                    }
                }
            } else if ($data['is_component'] == true) {
                $barang = Components::create([
                    'name' => $data['name'],
                    'serial_code' => $data['serial_code'],
                    'condition' => $data['condition'],
                    'type_id' => $typeId,
                ]);
            }

            if (is_null($barang)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Terjadi Kesalahan saat proses menyimpan Barang.'], 500);
                }
                return redirect()->back()->with('error', 'Terjadi Kesalahan saat proses menyimpan Barang.')->withInput();
            }

            $specs = $data['specifications'] ?? [];
            if (!empty($specs) && is_array($specs)) {
                foreach ($specs as $specRow) {
                    // Abaikan jika data tidak lengkap
                    if (empty($specRow['attribute']) || empty($specRow['value'])) {
                        continue;
                    }

                    // Panggil helper untuk membereskan atribut dan valuenya
                    $specValueId = $this->getOrCreateSpecification(
                        $specRow['attribute'],
                        $specRow['value']
                    );

                    $barang->specSetValues()->attach($specValueId);
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan!']);
            }

            return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error createItems: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage())->withInput();
        }
    }

    public function createType(Request $request)
    {
        $data = $request->only(['type_name']);
        $valid = Validator::make($data, [
            'type_name' => 'required|unique:types,name',
        ], [
            'type_name.required' => 'Mohon masukkan nama dari Tipe yang ingin dibuat.',
            'type_name.unique' => 'Tipe dengan nama ini sudah ada.'
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
        try {
            $type = $this->getOrCreateType('new::' . $data['type_name']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipe baru berhasil dibuat.',
                    'data' => [
                        'type_id' => $type
                    ],
                ]);
            }
            return redirect()->back()->with('success', 'Tipe baru berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Error createType: ' . $e->getMessage()); // Log untuk developer

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage())->withInput();
        }
    }

    public function updateCondition(Request $request, Items $item)
    {
        try {
            $item->condition = !$item->condition; // Toggle boolean (1 -> 0, 0 -> 1)
            $item->save();

            $newConditionText = $item->condition ? 'Bagus' : 'Rusak';

            return response()->json([
                'success' => true,
                'message' => "Kondisi '{$item->name}' berhasil diubah menjadi '{$newConditionText}'.",
                'new_condition' => $item->condition
            ]);
        } catch (\Exception $e) {
            Log::error('Error updateCondition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah kondisi item.'], 500);
        }
    }

    public function attachToDesk(Request $request, Items $item, Desks $desk)
    {
        // Validasi lagi untuk keamanan (mencegah race condition)
        if ($item->desk_id) {
            return response()->json(['success' => false, 'message' => 'Item ini sudah terpasang di meja lain.'], 409); // 409 Conflict
        }

        try {
            $item->desk_id = $desk->id;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' berhasil dipasang ke Meja '{$desk->location}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error attachToDesk: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memasang item ke meja.'], 500);
        }
    }

    public function getItemFilters()
    {
        $types = Type::all();
        $specifications = SpecAttributes::with('specValues')->get();

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil Type dan Spesifikasi yang ada',
            'data' => [
                'types' => $types,
                'specifications' => $specifications,
            ]
        ]);
    }

    public function getUnaffiliatedItems(Request $request)
    {
        $request->validate([
            'type_id' => 'nullable|uuid|exists:types,id',
            'spec_value_id' => 'nullable|uuid|exists:spec_set_value,id',
        ]);

        $typeId = $request->input('type_id');
        $specValueId = $request->input('spec_value_id');

        $query = Items::query()->where('condition', 1)->whereNull('desk_id'); // unaffiliated, kondisi bagus

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        if ($specValueId) {
            $query->whereHas('specSetValues', function (Builder $q) use ($specValueId) {
                $q->where('spec_set_value.id', $specValueId);
            });
        }

        $items = $query->orderBy('name')
            ->get(['id', 'name', 'serial_code']);

        return response()->json([
            'success' => true,
            'message' => 'Sukses menganmbil Item yang Avaliable dan Berfungsi',
            'data' => [
                'items' => $items
            ],
        ]);
    }

    private function upperWords(?string $text): string
    {
        return strtoupper(trim($text ?? ''));
    }

    private function getOrCreateType($type)
    {
        if (Str::startsWith($type, 'new::')) {
            $newTypeName = trim(Str::after($type, 'new::'));
            if (empty($newTypeName)) {
                throw new \Exception('Nama tipe baru tidak boleh kosong.');
            }
            $newTypeName = $this->upperWords($newTypeName ?? '');
            $type = Type::firstOrCreate(['name' => $newTypeName]);
            return $type->id;
        }

        return $type; // UUID
    }

    private function getOrCreateSpecification($attrInput, $valInput)
    {
        if (Str::startsWith($attrInput, 'new::')) {
            $attrName = trim(Str::after($attrInput, 'new::'));
            if (empty($attrName)) throw new \Exception("Nama Atribut tidak valid.");
            $attrName = $this->upperWords($attrName ?? '');
            $attribute = SpecAttributes::firstOrCreate(['name' => $attrName]);
        } else {
            $attrInput = $this->upperWords($attrInput ?? '');
            $attribute = SpecAttributes::find($attrInput);
            if (!$attribute) throw new \Exception("Attribute dengan ID '$attrInput' tidak ditemukan.");
        }
        if (Str::startsWith($valInput, 'new::')) {
            $valName = trim(Str::after($valInput, 'new::'));
            if (empty($valName)) throw new \Exception("Nilai Value tidak valid.");
            $valName = $this->upperWords($valName ?? '');
            $specValue = SpecSetValue::firstOrCreate([
                'spec_attributes_id' => $attribute->id,
                'value' => $valName
            ]);
        } else {
            $valInput = $this->upperWords($valInput ?? '');
            $specValue = SpecSetValue::find($valInput);
            if (!$specValue) throw new \Exception("Spec Value dengan ID '$valInput' tidak ditemukan.");

            if ($specValue->spec_attributes_id !== $attribute->id) {
                throw new \Exception("Data tidak cocok: Value '$specValue->value' bukan milik Attribute '$attribute->name'.");
            }
        }

        return $specValue->id;
    }
    private function processSpecifications($model, $specifications)
    {
        if (empty($specifications) || !is_array($specifications)) {
            return;
        }

        foreach ($specifications as $specRow) {
            if (empty($specRow['attribute']) || empty($specRow['value'])) {
                continue;
            }

            $specValueId = $this->getOrCreateSpecification(
                $specRow['attribute'],
                $specRow['value']
            );

            $model->specSetValues()->attach($specValueId);
        }
    }
}
