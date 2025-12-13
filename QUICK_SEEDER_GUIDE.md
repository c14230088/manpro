# Quick Seeder Guide 🚀

## Perintah Cepat

```bash
# Reset database dan seed ulang (RECOMMENDED)
php artisan migrate:fresh --seed

# Hanya seed tanpa reset
php artisan db:seed

# Seed seeder tertentu
php artisan db:seed --class=NamaSeeder
```

---

## Data yang Akan Di-Generate

| Item | Jumlah |
|------|--------|
| 👥 Users | 35 orang |
| 🏢 Units | 9 unit |
| 📅 Periods | 7 periode |
| 🔬 Labs | 8 lab |
| 🪑 Desks | 300+ meja |
| 💻 Items | 1,500+ items |
| 🔧 Components | 1,500+ components |
| 📦 Sets | 35 sets |
| 💿 Software | 40 software |
| 📋 Bookings | 150 bookings |
| 🔨 Repairs | 100 repairs |
| 📁 Folders | 9 folders |

**Total: ~10,000+ records**

---

## Seeder yang Sudah Diperbaiki ✅

1. ✅ **UnitSeeder** - 9 units + permissions
2. ✅ **PeriodSeeder** - 7 periode (2022-2026)
3. ✅ **UserSeeder** - 35 users (dari 7)
4. ✅ **LabsSeeder** - 8 labs
5. ✅ **DesksSeeder** - 300+ desks
6. ✅ **TypeSeeder** - 19 types
7. ✅ **SetSeeder** - 35 sets (dari 10)
8. ✅ **specification** - Spec attributes & values
9. ✅ **ItemsSeeder** - 1,500+ items
10. ✅ **ComponentsSeeder** - 1,500+ components
11. ✅ **ToolSpecSeeder** - 6,000+ tool specs
12. ✅ **SoftwareSeeder** - 40 software (dari 15)
13. ✅ **BookingSeeder** - 150 bookings (dari 50)
14. ✅ **RepairSeeder** - 100 repairs (dari 40)
15. ✅ **FilesSeeder** - 9 folders (baru)

---

## Perubahan Utama

### 🔥 UserSeeder
- **+28 users baru**
- Semua unit punya user
- Email realistis

### 🔥 SetSeeder
- **+25 sets baru**
- Tambahan: SET LAPTOP & SET GAMING

### 🔥 SoftwareSeeder
- **+25 software baru**
- Total 40 software lengkap

### 🔥 BookingSeeder
- **+100 bookings baru**
- Total 150 bookings

### 🔥 RepairSeeder
- **+60 repairs baru**
- Total 100 repairs

### 🔥 FilesSeeder
- **Baru dibuat**
- 9 folders dengan struktur

---

## Login Credentials

### Super Admin
- Email: `c14230088@john.petra.ac.id`
- Unit: ADMIN

### Sample Users
- Admin: `admin@petra.ac.id` (PTIK)
- Dosen: `john.doe@petra.ac.id` (DOSEN)
- Mahasiswa: `c14230001@john.petra.ac.id` (MAHASISWA)
- Kepala Lab: `kepala.lab@petra.ac.id` (KEPALA LAB)

---

## Troubleshooting

```bash
# Error "Class not found"
composer dump-autoload

# Error "Foreign key"
# Cek urutan di DatabaseSeeder.php

# Error "Duplicate entry"
php artisan migrate:fresh --seed

# Lihat data di database
php artisan tinker
>>> User::count()
>>> Booking::count()
>>> Items::count()
```

---

## File Dokumentasi

1. 📄 **SEEDER_IMPROVEMENTS.md** - Detail lengkap (English)
2. 📄 **RINGKASAN_SEEDER.md** - Ringkasan (Indonesia)
3. 📄 **QUICK_SEEDER_GUIDE.md** - Guide ini

---

## Status: ✅ READY TO USE

Semua seeder sudah diperbaiki dan siap digunakan!

```bash
php artisan migrate:fresh --seed
```

**Selamat testing! 🎉**
