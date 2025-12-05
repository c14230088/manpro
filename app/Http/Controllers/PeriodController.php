<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodController extends Controller
{
    public function periods()
    {
        $periods = Period::all();

        return view('admin.periods', [
            'title' => 'Academic Periods',
            'periods' => $periods
        ]);
    }

    public function createPeriod(Request $request)
    {
        $data = $request->only('academic_year', 'semester', 'active');

        $valid = Validator::make(
            $data,
            [
                'academic_year' => [
                    'required',
                    'string',
                    'size:9',
                    'regex:/^\d{4}\/\d{4}$/',
                    function ($attribute, $value, $fail) use ($data) {
                        $startYear = (int)substr($value, 0, 4);
                        $endYear = (int)substr($value, 5, 4);
                        if ($startYear > $endYear) {
                            $fail('Tahun ajaran tidak valid. Tahun pertama harus lebih kecil dari tahun kedua.');
                        }
                        if ($endYear !== $startYear + 1) {
                            $fail('Tahun ajaran tidak valid. Tahun kedua harus satu tahun lebih besar dari tahun pertama.');
                        }
                        
                        // Cek kombinasi academic_year dan semester unik
                        $exists = Period::where('academic_year', $value)
                            ->where('semester', $data['semester'])
                            ->exists();
                        if ($exists) {
                            $fail('Periode akademik dengan tahun ajaran ' . $value . ' dan semester ' . $data['semester'] . ' sudah ada.');
                        }
                    },
                ],
                'semester' => 'required|in:GASAL,GENAP',
                'active' => 'sometimes|boolean',
            ],
            [
                'academic_year.required' => 'Tahun ajaran wajib diisi.',
                'academic_year.size' => 'Tahun ajaran harus berformat "YYYY/YYYY".',
                'academic_year.regex' => 'Tahun ajaran harus berformat "YYYY/YYYY".',
                'semester.required' => 'Semester wajib dipilih.',
                'semester.in' => 'Semester harus bernilai GASAL atau GENAP.',
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
        
        $message = 'Periode akademik berhasil ditambahkan.';
        // Jika active true, set semua period lain jadi false
        if (isset($data['active']) && $data['active']) {
            Period::where('active', true)->update(['active' => false]);
            $message = 'Periode akademik berhasil ditambahkan dan diaktifkan.';
        } else {
            $data['active'] = false;
        }

        Period::create($data);
        
        if($request->wantsJson()){
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    public function setActivePeriod(Request $request, $id)
    {
        $period = Period::find($id);
        if (!$period) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode akademik yang akan diaktivkan tidak ditemukan.',
                ]);
            }
            return redirect()->back()->with('error', 'Periode akademik yang akan diaktivkan tidak ditemukan.');
        }

        // Set semua period lain jadi false
        Period::where('active', true)->update(['active' => false]);

        // Set period ini jadi active
        $period->active = true;
        $period->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Periode akademik ' . $period->academic_year . ' ' . $period->semester . ' berhasil diaktifkan.',
            ]);
        }
        return redirect()->back()->with('success', 'Periode akademik berhasil diaktifkan.');
    }
}
