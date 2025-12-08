# Booking System Redesign - Implementation Plan

## Overview
Redesign booking system to support:
- Lab booking (existing)
- Set booking (auto-find N complete desks with Monitor, Mouse, Keyboard, CPU)
- Individual item/component booking via desk map browsing

## Required Changes

### 1. Database Changes
**bookings_items table** - Add fields:
- `desk_id` (uuid, nullable) - track which desk item came from
- `quantity` (integer, default 1) - for set bookings

### 2. Backend API Endpoints

#### A. Get Available Sets
```
GET /api/labs/{lab}/available-sets?start={datetime}&end={datetime}
Response: { available_count: 10, desks: [...] }
```
Logic: Find desks with all 4 types (Monitor, Mouse, Keyboard, CPU), all items condition=1, not under repair, not booked in timeframe.

#### B. Get Desk Map with Availability
```
GET /api/labs/{lab}/desk-map?start={datetime}&end={datetime}
Response: [{ id, location, items: [{ id, name, type, available, condition }] }]
```
Logic: Return all desks with items, mark each item as available/unavailable based on booking conflicts.

#### C. Store Booking (Updated)
```
POST /api/bookings
Body: {
  booking_type: 'lab'|'sets'|'items',
  lab_id: uuid (for lab booking),
  sets: [{ lab_id, quantity }], (for set booking)
  items: [{ item_id, desk_id }], (for individual items)
  ...other booking fields
}
```

### 3. Frontend Components

#### A. Booking Type Selection
- Radio: Lab / Sets / Individual Items

#### B. Set Booking Flow
1. Select lab(s)
2. Input quantity per lab
3. System validates availability
4. Show which desks will be reserved

#### C. Individual Item Booking Flow
1. Select lab
2. Show desk map (grid layout)
3. Click desk → modal shows items on that desk
4. Click item → add to cart
5. Cart shows all selected items
6. Can browse multiple labs

### 4. Validation Rules

#### Set Booking
- Check N desks exist with complete set (4 types)
- All items must be condition=1
- No items under repair
- No booking conflicts in timeframe

#### Individual Item Booking
- Item must be condition=1
- Not under repair
- Not booked in timeframe
- Track desk_id for each item

### 5. Key Files to Create/Modify

**New Files:**
- `app/Http/Controllers/Api/BookingApiController.php`
- `resources/views/user/booking-new.blade.php`
- `public/js/booking-cart.js`

**Modified Files:**
- `app/Http/Controllers/BookingController.php` (storeBooking method)
- `app/Models/Booking.php` (relationships)
- `database/migrations/xxxx_update_bookings_items_table.php`
- `routes/api.php` (new endpoints)

## Implementation Priority

1. **Phase 1**: Database migration + Set availability API
2. **Phase 2**: Individual item desk map API
3. **Phase 3**: Frontend booking form with cart
4. **Phase 4**: Backend booking storage logic
5. **Phase 5**: Testing & validation

## Estimated Complexity
- **High** - Requires 500+ lines of new code
- **Time**: 4-6 hours for full implementation
- **Risk**: Breaking existing booking system

## Recommendation
Due to complexity, implement incrementally:
1. Start with Set booking only
2. Add individual item booking later
3. Keep existing lab booking unchanged

Would you like me to proceed with Phase 1 (Set booking only) first?
