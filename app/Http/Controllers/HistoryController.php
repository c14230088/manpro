<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bookings_item;
use App\Models\labs;
use App\Models\Period; // Pastikan Model Period di-import
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function historylabs(Request $request)
    {
        // 1. Data Pendukung Dropdown (Hanya dipakai saat load halaman pertama)
        $periods = Period::orderBy('id', 'desc')->get();
        $labs = Labs::orderBy('name', 'asc')->get();

        // 2. QUERY DASAR (Base Query)
        $query = Booking::query()
            ->join('bookings_items', 'bookings.id', '=', 'bookings_items.booking_id')
            ->join('labs', 'bookings_items.bookable_id', '=', 'labs.id')
            ->where('bookings_items.bookable_type', 'App\\Models\\Labs');


        // Filter Periode
        if ($request->filled('period_id')) {
            $query->where('bookings.period_id', $request->period_id);
        }

        // Filter Lab
        if ($request->filled('lab_id')) {
            $query->where('labs.id', $request->lab_id);
        }

        // Filter Search (Nama/Email Peminjam)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('borrower', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('bookings.borrowed_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('bookings.borrowed_at', '<=', $request->date_end);
        }

        // Filter Status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'approved': 
                    $query->where('bookings.approved', 1)
                        ->whereNull('bookings_items.returned_at')
                        ->whereNull('bookings_items.returner_id')
                        ->whereNull('bookings_items.returned_status')
                        ->where('bookings.return_deadline_at', '>=', now());
                    break;

                case 'rejected':
                    $query->where('bookings.approved', 0)->whereNull('bookings_items.returned_at');
                    break;

                case 'pending':
                    $query->whereNull('bookings.approved');
                    break;

                case 'completed':
                    $query->whereNotNull('bookings_items.returned_at');
                    break;

                case 'overdue':
                    $query->where('bookings.approved', 1)
                        ->whereNull('bookings_items.returned_at')
                        ->where('bookings.return_deadline_at', '<', now());
                    break;
            }
        }

        // 3. HITUNG INSIGHT/STATISTIK (Berdasarkan Filter di atas)

        // A. Total Peminjaman (Sesuai Filter)
        $totalBookings = $query->clone()->count();

        // B. Top Lab (Sesuai Filter)
        $topLabData = $query->clone()
            ->select('labs.name as lab_name', DB::raw('count(*) as total'))
            ->groupBy('labs.id', 'labs.name')
            ->orderByDesc('total')
            ->first();

        // 4. AMBIL DATA TABEL (Pagination)
        $histories = $query->select(
            'bookings.*',
            'labs.name as lab_name',
            'bookings_items.returned_at',      
            'bookings_items.returned_status',  
            'bookings_items.returner_id'       
        )
            ->with(['borrower', 'period']) 
            ->latest('bookings.borrowed_at')
            ->paginate(10);

        // 5. DETEKSI AJAX REQUEST
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partials.history_labs_table', compact('histories'))->render(),

                'stats' => [
                    'total_bookings' => number_format($totalBookings),
                    'top_lab_name'   => $topLabData->lab_name ?? '-',
                    'top_lab_count'  => $topLabData->total ?? 0,
                    'is_filtered'    => $request->anyFilled(['period_id', 'date_start', 'date_end', 'status', 'lab_id', 'search'])
                ]
            ]);
        }

        return view('admin.booking-lab-history', compact(
            'histories',
            'periods',
            'labs',
            'totalBookings',
            'topLabData'
        ));
    }
}
