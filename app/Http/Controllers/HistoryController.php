<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Bookings_item;
use App\Models\labs;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Type;

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
        $dateType = $request->input('date_type', 'borrow_date');

        // Tentukan kolom database berdasarkan pilihan user
        $dateColumn = 'bookings.borrowed_at'; // Default
        switch ($dateType) {
            case 'due_date':
                $dateColumn = 'bookings.return_deadline_at';
                break;
            case 'return_date':
                $dateColumn = 'bookings_items.returned_at';
                break;
            default:
                $dateColumn = 'bookings.borrowed_at';
                break;
        }

        // Terapkan Filter pada kolom yang terpilih
        if ($request->filled('date_start')) {
            $query->whereDate($dateColumn, '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate($dateColumn, '<=', $request->date_end);
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

    public function historyinventaris(Request $request)
    {
        // 1. DATA PENDUKUNG DROPDOWN
        $periods = Period::orderBy('id', 'desc')->get();
        $labs = Labs::orderBy('name', 'asc')->get();
        $types = Type::orderBy('name', 'asc')->get();

        // 2. QUERY DASAR
        $query = Bookings_item::query()
            ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
            ->where('bookings_items.bookable_type', '!=', 'App\\Models\\Labs')
            ->select(
                'bookings_items.*',
                'bookings.created_at as booking_created_at',
                'bookings.borrowed_at as plan_borrowed_at',
                'bookings.event_name',
                'bookings.approved',
                'bookings.return_deadline_at',
                'bookings.period_id'
            )
            ->with([
                'booking.borrower',
                'bookable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Items::class => ['desk.lab', 'type'],
                        \App\Models\Components::class => ['item.desk.lab', 'type'],
                    ]);
                }
            ]);

        // --- 3. LOGIKA FILTER ---

        // A. Filter Periode
        if ($request->filled('period_id')) {
            $query->where('bookings.period_id', $request->period_id);
        }

        // B. Filter Laboratorium (PERBAIKAN UTAMA DISINI)
        if ($request->filled('lab_id')) {
            $query->whereHasMorph(
                'bookable',
                [\App\Models\Items::class, \App\Models\Components::class],
                function (Builder $q, $type) use ($request) {
                    if ($type === \App\Models\Items::class) {
                        $q->whereHas('desk', function ($subQ) use ($request) {
                            $subQ->where('lab_id', $request->lab_id);
                        });
                    }
                    elseif ($type === \App\Models\Components::class) {
                        $q->whereHas('item.desk', function ($subQ) use ($request) {
                            $subQ->where('lab_id', $request->lab_id);
                        });
                    }
                }
            );
        }

        // C. Filter Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('booking.borrower', function ($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                })
                    ->orWhere('bookings.event_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHasMorph(
                        'bookable',
                        '*',
                        function (Builder $subQ) use ($searchTerm) {
                            $subQ->where('name', 'LIKE', "%{$searchTerm}%");
                        }
                    );
            });
        }

        // D. Filter Tanggal
        $dateType = $request->input('date_type', 'borrow_date');

        $dateColumn = 'bookings.borrowed_at'; 
        switch ($dateType) {
            case 'due_date':
                $dateColumn = 'bookings.return_deadline_at';
                break;
            case 'return_date':
                $dateColumn = 'bookings_items.returned_at';
                break;
            default:
                $dateColumn = 'bookings.borrowed_at';
                break;
        }

        // Terapkan Filter pada kolom yang terpilih
        if ($request->filled('date_start')) {
            $query->whereDate($dateColumn, '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate($dateColumn, '<=', $request->date_end);
        }

        // E. Filter Status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'approved':
                    $query->where('bookings.approved', 1)
                        ->whereNull('bookings_items.returned_at')
                        ->where('bookings.return_deadline_at', '>=', now());
                    break;
                case 'rejected':
                    $query->where('bookings.approved', 0);
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
        // F. FILTER TIPE BARANG (BARU)
        if ($request->filled('type_id')) {
            $query->whereHasMorph(
                'bookable',
                [\App\Models\Items::class, \App\Models\Components::class],
                function (Builder $q) use ($request) {
                    $q->where('type_id', $request->type_id);
                }
            );
        }

        // --- 4. HITUNG STATISTIK ---

        $totalBookings = $query->clone()->count();

        $statsCollection = $query->clone()
            ->select('bookings_items.bookable_id', 'bookings_items.bookable_type') 
            ->with(['bookable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Items::class => ['type'],
                    \App\Models\Components::class => ['type'],
                ]);
            }])
            ->get(); 

        $topTypeStats = $statsCollection
            ->map(function ($row) {
                return $row->bookable->type->name ?? 'Tanpa Tipe';
            })
            ->countBy()   
            ->sortDesc(); 

        $topItemName  = $topTypeStats->keys()->first() ?? '-'; 
        $topItemCount = $topTypeStats->first() ?? 0;           

        $query->orderBy('bookings.created_at', 'desc');
        $histories = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partials.history_itemscomp_table', compact('histories'))->render(),
                'stats' => [
                    'total_bookings' => number_format($totalBookings),
                    'top_item_name'  => $topItemName,
                    'top_item_count' => $topItemCount,
                    'is_filtered'    => $request->anyFilled(['period_id', 'date_start', 'date_end', 'status', 'lab_id', 'search', 'type_id'])
                ]
            ]);
        }

        return view('admin.booking-itemcomp-history', compact('histories', 'periods', 'labs','types' ,'totalBookings', 'topItemName', 'topItemCount'));
    }
}
