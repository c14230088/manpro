# Database Seeding Instructions

## Overview
I've created comprehensive seeders to populate your database with realistic test data for a medium-sized system.

## What's Included

### 1. **UserSeeder**
- 1 Admin user
- 3 Supervisor/Lecturer users  
- 5 Student users
- All passwords: `password`

### 2. **PeriodSeeder**
- Academic periods for 2023/2024 and 2024/2025
- Current active period: 2024/2025 GANJIL

### 3. **TypeSeeder**
- Equipment types: Monitor, PC, Keyboard, Mouse, VR Headset, etc.
- Component types: Processor, RAM, Storage, Graphics Card, etc.

### 4. **SetSeeder**
- Equipment sets like "BASIC COMPUTER SET A", "VR DEVELOPMENT SET"

### 5. **ItemsSeeder**
- Creates items for each lab and desk
- ~280+ items total (monitors, PCs, keyboards, mice, VR headsets)
- 90% in good condition, 10% broken for testing
- Proper lab and desk assignments

### 6. **ComponentsSeeder**
- Creates components for each PC item
- Processors, RAM, Storage, Graphics Cards, Motherboards, Power Supplies
- ~1400+ components total
- Realistic condition distribution

### 7. **SpecificationSeeder**
- Adds technical specifications to items and components
- Brands, models, capacities, speeds, resolutions, etc.
- Random assignment for realistic variety

### 8. **BookingSeeder**
- 20 realistic booking scenarios
- Mix of lab, item, and component bookings
- Various approval statuses (pending, approved, rejected)
- Some with return records, some still active
- Realistic event names and details

### 9. **RepairSeeder**
- Repair records for broken items and components
- Various repair statuses and issue descriptions
- Realistic repair timelines and success rates

## How to Run

### Step 1: Clear existing data (optional)
```bash
php artisan migrate:fresh
```

### Step 2: Run all seeders
```bash
php artisan db:seed
```

### Step 3: Test the system
Visit `/booking-test` to test the booking APIs and functionality.

## Test Data Summary

After seeding, you'll have:
- **7 Labs** with 10 desks each
- **280+ Items** (monitors, PCs, keyboards, mice, VR headsets)
- **1400+ Components** (processors, RAM, storage, etc.)
- **20 Bookings** with various statuses
- **25 Repair records** for broken equipment
- **8 Users** (admin, supervisors, students)
- **Specifications** attached to all items and components

## Test Scenarios You Can Try

### 1. **Booking System**
- Visit `/booking` for the main booking form
- Visit `/booking-test` for API testing
- Try booking different types (lab, item, component)
- Test availability filtering by date and lab

### 2. **API Endpoints**
- `/booking/available-items?start_date=2024-12-10T09:00&end_date=2024-12-10T17:00`
- `/booking/available-components?lab_id=<lab-uuid>&start_date=...&end_date=...`
- `/booking/available-labs?start_date=...&end_date=...`

### 3. **Data Relationships**
- Items belong to labs and desks
- Components belong to items
- Bookings can be for labs, items, or components
- Specifications are attached to items and components
- Repair records track broken equipment

## Login Credentials

### Admin
- Email: `admin@petra.ac.id`
- Password: `password`

### Supervisors
- Email: `john.doe@petra.ac.id`, `jane.smith@petra.ac.id`, `michael.johnson@petra.ac.id`
- Password: `password`

### Students
- Email: `alice@student.petra.ac.id`, `bob@student.petra.ac.id`, etc.
- Password: `password`

## Troubleshooting

If you encounter any errors:

1. **Clear cache**: `php artisan cache:clear`
2. **Clear config**: `php artisan config:clear`
3. **Regenerate autoload**: `composer dump-autoload`
4. **Check logs**: `storage/logs/laravel.log`

The system is now ready for comprehensive testing with realistic data that simulates a medium-sized laboratory management system!