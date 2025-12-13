# Admin Bookings System Rules

## Design Patterns
- **Consistent UI**: All admin pages must match repairs.blade.php styling (card layouts, shadows, borders, proper spacing)
- **No jQuery**: Use vanilla JavaScript and fetch API exclusively
- **Professional Design**: Clean, modern interface with proper visual hierarchy

## Database & Models Structure
### Items Model
- Relationships: `desk`, `lab`, `unit`, `components`, `type`, `specSetValues`
- Location: Via `desk.lab` or direct `lab` relationship (NOT lab_storage or table)
- Specifications: Via `specSetValues.specAttributes` relationship (NOT a direct field)

### Components Model
- Relationships: `item`, `lab`, `unit`, `type`, `specSetValues`
- Location: Via `item.desk.lab` or direct `lab`

### Labs Model
- Relationships: `desks`, `items`, `softwares`
- Fields: `location`, `capacity`

### Booking Model
- Relationships: `borrower`, `supervisor`, `approver` (User), `period`, `bookings_items`

### Bookings_item Model (Pivot)
- `morphTo` relationship for `bookable` (polymorphic)
- Return tracking fields: `returned_at`, `return_condition`, `return_notes`

## Eager Loading Pattern
**CRITICAL**: Use `morphWith` for polymorphic relationships to prevent errors:

```php
$bookings = Booking::with([
    'borrower.unit',
    'supervisor.unit', 
    'approver.unit',
    'period',
    'bookings_items' => function($query) {
        $query->with([
            'bookable' => function($query) {
                $query->morphWith([
                    Items::class => ['desk.lab', 'lab', 'components', 'type', 'specSetValues.specAttributes'],
                    Components::class => ['item.desk.lab', 'item.lab', 'lab', 'type', 'specSetValues.specAttributes'],
                    Labs::class => ['desks', 'items']
                ]);
            }
        ]);
    }
])->get();
```

## Filter System
### Required Filters
1. **Status**: pending, approved, rejected, returned
2. **Academic Year**: Separate filter (e.g., 2023/2024)
3. **Semester**: Separate filter (Ganjil/Genap)
4. **Type**: PC Set, Other Items, Components, Labs
5. **Return Status**: complete, complete_damaged, incomplete, not_returned
6. **Deadline**: overdue, upcoming

### Return Status Calculation
```php
$allReturned = $booking->bookings_items->every(fn($item) => $item->returned_at !== null);
$hasDamage = $booking->bookings_items->contains(fn($item) => $item->return_condition === 'damaged');
$someReturned = $booking->bookings_items->some(fn($item) => $item->returned_at !== null);

if ($allReturned && !$hasDamage) return 'complete';
if ($allReturned && $hasDamage) return 'complete_damaged';
if ($someReturned) return 'incomplete';
return 'not_returned';
```

### Deadline Calculation
```php
use Carbon\Carbon;
Carbon::setLocale('id');
$now = Carbon::now('Asia/Jakarta');
$deadline = Carbon::parse($booking->return_deadline_at, 'Asia/Jakarta');

if ($now->gt($deadline)) return 'overdue';
if ($now->diffInDays($deadline) <= 3) return 'upcoming';
return 'normal';
```

## Data Attributes for Filtering
Each table row must include:
- `data-status`: booking status
- `data-year`: academic year (e.g., "2023/2024")
- `data-semester`: semester (Ganjil/Genap)
- `data-types`: JSON array of item types
- `data-return`: return status (complete/complete_damaged/incomplete/not_returned)
- `data-deadline`: deadline status (overdue/upcoming/normal)

## User Display Pattern
Show user information with UNIT name:
```html
<div>
    <div class="font-medium">{{ $user->name }}</div>
    @if($user->unit)
        <div class="text-sm text-gray-500">{{ $user->unit->name }}</div>
    @endif
</div>
```

## Booking Summary (Ringkasan Peminjaman)
Calculate and display:
- **PC Set Count**: Count items where type is "PC" (1 PC + components = 1 set)
- **Other Items**: Count non-PC items
- **Components**: Count standalone components
- **Labs**: Count lab bookings

## Item Details Display
Must show:
1. **Type**: Item/Component/Lab type name
2. **Specifications**: From specSetValues relationship
3. **Location**: 
   - Items: desk.lab.name or lab.name
   - Components: item.desk.lab.name or item.lab.name or lab.name
   - Labs: location field
4. **Components**: For PC items, show associated components with details

## Modal Behavior
- Click outside modal to close
- ESC key to close
- Professional styling with shadows and borders
- Smooth transitions

## Return Form UX
- Clear instructional text explaining the process
- Field labels for condition and notes
- Individual return tracking per item
- Validation feedback

## Pagination
- DataTable pagination must be full width
- CSS: `#bookings-table + div { width: 100% !important; }`

## Routes Structure
```php
Route::get('/admin/bookings', [BookingController::class, 'bookings']);
Route::get('/admin/bookings/{booking}/details', [BookingController::class, 'getBookingDetails']);
Route::post('/admin/bookings/{booking}/approve', [BookingController::class, 'approveBooking']);
Route::post('/admin/bookings/{booking}/return', [BookingController::class, 'returnBooking']);
```

## Menu Integration
Add "Bookings" menu item in admin sidebar (desktop and mobile) after "Repairs" menu item.

## TomSelect Integration
Use TomSelect for all filter dropdowns with consistent styling and behavior.

## Timezone
Always use 'Asia/Jakarta' timezone for date/time operations with Carbon.
