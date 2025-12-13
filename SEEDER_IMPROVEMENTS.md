# Dokumentasi Peningkatan Seeder

## Ringkasan Perubahan

Semua seeder telah diperbaiki dan ditambahkan data untuk memperbanyak data sistem. Berikut adalah detail perubahan untuk setiap seeder:

---

## 1. **UnitSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- 9 Unit kerja (MAHASISWA, TU INFOR, KEPALA LAB, ASISTEN DOSEN, ASISTEN LAB, DOSEN, PTIK, UPPK, ADMIN)
- Permission Groups dan Permissions lengkap
- 1 Super Admin user
- Semua permissions di-assign ke ADMIN unit

---

## 2. **PeriodSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak periode akademik

**Data yang di-seed:**
- **Sebelumnya:** 4 periode
- **Sekarang:** 7 periode (2022/2023 - 2025/2026)
- Periode aktif: 2024/2025 GASAL

---

## 3. **UserSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak user untuk semua unit

**Data yang di-seed:**
- **Sebelumnya:** ~7 users
- **Sekarang:** ~35 users
  - 1 Admin System
  - 2 TU INFOR Staff
  - 1 Kepala Lab
  - 3 Asisten Lab
  - 6 Dosen
  - 2 Asisten Dosen
  - 2 UPPK Staff
  - 18 Mahasiswa

---

## 4. **LabsSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- 8 Laboratorium dengan lokasi dan kapasitas

---

## 5. **DesksSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- Total ~300+ meja untuk semua lab
- Setiap lab memiliki meja dengan lokasi spesifik (A1, B2, C3, dll)

---

## 6. **TypeSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- 19 tipe peralatan (Monitor, CPU, Keyboard, Mouse, VR Headset, Processor, RAM, Storage, dll)

---

## 7. **SetSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak set dan variasi

**Data yang di-seed:**
- **Sebelumnya:** 10 set PC
- **Sekarang:** 35 set total
  - 30 SET PC standar (PC-1 sampai PC-30)
  - 3 SET LAPTOP (ASUS, LENOVO, HP)
  - 2 SET GAMING

---

## 8. **specification.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- 7 Spec Attributes (BRAND, MODEL, CAPACITY, RESOLUTION, REFRESH_RATE, TYPE, SPEED)
- ~50 Spec Values untuk berbagai atribut

---

## 9. **ItemsSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- Items untuk setiap desk di setiap lab (Monitor, CPU, Keyboard, Mouse)
- VR Headsets untuk Lab VR
- Storage items untuk setiap lab
- Total: ~1500+ items

---

## 10. **ComponentsSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- Components untuk setiap CPU item (Processor, RAM, Storage)
- Total: ~1500+ components

---

## 11. **ToolSpecSeeder.php** ✅
**Status:** Sudah lengkap (tidak ada perubahan)

**Data yang di-seed:**
- Random specifications untuk items (2-4 specs per item)
- Random specifications untuk components (1-3 specs per component)

---

## 12. **SoftwareSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak software

**Data yang di-seed:**
- **Sebelumnya:** 15 software
- **Sekarang:** 40 software
  - IDE & Editors (VS Code, IntelliJ, PyCharm, Android Studio, dll)
  - Game Engines (Unity, Unreal Engine)
  - 3D Modeling (Blender, Maya, AutoCAD, SolidWorks)
  - Adobe Suite (Photoshop, Premiere, Illustrator, After Effects)
  - Database Tools (MySQL, PostgreSQL, MongoDB)
  - Development Tools (Docker, Git, Postman, Node.js, Python, Java)
  - Network Tools (Wireshark, Cisco Packet Tracer)
  - Virtualization (VMware, VirtualBox)
  - Data Science (Anaconda, Jupyter, R Studio, Tableau, Power BI)
- Software di-attach ke lab secara random (5-10 software per lab)

---

## 13. **BookingSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak booking

**Data yang di-seed:**
- **Sebelumnya:** 50 bookings
- **Sekarang:** 150 bookings
- Berbagai tipe booking:
  - Lab booking
  - Items booking
  - Components booking
- Status approval yang bervariasi (approved, pending, rejected)
- Dengan data returner, returned_at, returned_status

---

## 14. **RepairSeeder.php** ✅
**Perubahan:** Ditambahkan lebih banyak repair records

**Data yang di-seed:**
- **Sebelumnya:** 40 repairs (20 items + 20 components)
- **Sekarang:** 100 repairs (50 items + 50 components)
- Status repair yang bervariasi (Pending, In Progress, Completed, Failed)
- Dengan issue description dan repair notes yang realistis

---

## 15. **PermissionSeeder.php** ✅
**Status:** Kosong (permissions di-seed di UnitSeeder)

**Catatan:** Seeder ini dibiarkan kosong karena semua permissions sudah di-seed di UnitSeeder

---

## 16. **FilesSeeder.php** ✅
**Perubahan:** Dibuat dari awal

**Data yang di-seed:**
- 5 Root folders (Documents, Images, Videos, Reports, Manuals)
- 4 Subfolders:
  - Documents/Lab Procedures
  - Documents/Equipment Specs
  - Reports/Monthly
  - Reports/Annual
- Dengan owner_id dan open_public settings

---

## 17. **SpecificationSeeder.php** ✅
**Status:** Sudah ada tapi tidak digunakan di DatabaseSeeder

**Catatan:** Seeder ini mirip dengan specification.php. Gunakan specification.php yang sudah ada di DatabaseSeeder.

---

## Hubungan Antar Model

### Diagram Hubungan:
```
Unit
├── hasMany Users
└── morphToMany Permissions

User
├── belongsTo Unit
├── hasMany Bookings (as borrower/returner/approver)
├── hasMany Repairs (as reporter)
└── morphToMany Permissions

Period
├── hasMany Bookings
└── hasMany Repairs

Labs
├── hasMany Desks
├── hasMany Items
├── belongsToMany Software
└── morphToMany Bookings

Desks
├── belongsTo Labs
└── hasMany Items

Type
├── hasMany Items
└── hasMany Components

Set
└── hasMany Items

Items
├── belongsTo Desk/Lab/Unit/Type/Set
├── hasMany Components
├── morphToMany SpecSetValue
├── morphToMany Repairs
└── morphToMany Bookings

Components
├── belongsTo Item/Lab/Unit/Type
├── morphToMany SpecSetValue
├── morphToMany Repairs
└── morphToMany Bookings

SpecAttributes
└── hasMany SpecSetValue

Software
└── belongsToMany Labs

Booking
├── belongsTo User (borrower/supervisor/approver)
├── belongsTo Period
└── hasMany Bookings_item

Repair
├── belongsTo User (reporter)
├── belongsTo Period
└── hasMany Repairs_item

Permission_group
└── hasMany Permissions

Permission
├── belongsTo Permission_group
└── hasMany Model_permission

Folders
├── belongsTo User (owner)
└── belongsTo Folders (parent)
```

---

## Cara Menjalankan Seeder

### 1. Reset dan Seed Ulang Database:
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Seeder Tertentu:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=BookingSeeder
php artisan db:seed --class=SoftwareSeeder
```

### 3. Jalankan Semua Seeder:
```bash
php artisan db:seed
```

---

## Statistik Data Setelah Seeding

| Model | Jumlah Data |
|-------|-------------|
| Units | 9 |
| Users | ~35 |
| Periods | 7 |
| Labs | 8 |
| Desks | ~300+ |
| Types | 19 |
| Sets | 35 |
| Items | ~1500+ |
| Components | ~1500+ |
| SpecAttributes | 7 |
| SpecSetValue | ~50 |
| Tool_specs | ~6000+ |
| Software | 40 |
| Lab_Software | ~80+ |
| Bookings | 150 |
| Bookings_items | ~200+ |
| Repairs | 100 |
| Repairs_items | 100 |
| Permission_groups | 7 |
| Permissions | ~30 |
| Folders | 9 |

**Total Records: ~10,000+ records**

---

## Catatan Penting

1. **Urutan Seeding:** Urutan di DatabaseSeeder sudah benar dan harus diikuti karena ada dependency antar tabel
2. **UUID:** Semua model menggunakan UUID sebagai primary key
3. **Faker Data:** Beberapa data menggunakan faker untuk variasi (tanggal, boolean, dll)
4. **Kondisi:** Items dan Components memiliki kondisi random (85-90% baik)
5. **Relationships:** Semua relationships sudah di-setup dengan benar di seeder

---

## Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "Foreign key constraint"
Pastikan urutan seeder di DatabaseSeeder benar

### Error: "Duplicate entry"
```bash
php artisan migrate:fresh --seed
```

---

## Kesimpulan

Semua seeder telah diperbaiki dan ditambahkan data untuk memperbanyak data sistem. Sistem sekarang memiliki:
- ✅ Data user yang lengkap untuk semua unit
- ✅ Data lab dan equipment yang realistis
- ✅ Data booking dan repair yang banyak
- ✅ Data software yang lengkap
- ✅ Data permission yang terstruktur
- ✅ Data folders untuk file management

Total data: **~10,000+ records** siap untuk testing dan development!
