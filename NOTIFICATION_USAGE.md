# Dokumentasi Fitur Notifikasi Status Laporan

## Cara Penggunaan

### 1. Menambahkan Notifikasi ke Layout

Tambahkan komponen notification bell di layout Anda:

```blade
<!-- Di header atau navbar -->
@include('layouts.partials.notification-bell')

<!-- Pastikan sudah ada CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. Memastikan JavaScript Terload

Pastikan file JavaScript sudah termuat di layout:

```blade
@if(auth()->check())
    <script src="{{ asset('js/status-notifications.js') }}"></script>
@endif
```

### 3. Testing Notifikasi

Untuk testing notifikasi, Anda bisa:

1. Login sebagai user
2. Buat laporan baru atau ubah status laporan
3. Notifikasi akan muncul otomatis

### 4. API Endpoints

- `GET /api/status-notifications/unread-count` - Jumlah notifikasi belum dibaca
- `GET /api/status-notifications` - Daftar notifikasi
- `POST /api/status-notifications/mark-all-read` - Tandai semua dibaca

### 5. Troubleshooting

Jika terdapat error:

1. Jalankan migrasi untuk memastikan field `is_read` ada:
```bash
php artisan migrate
```

2. Clear cache jika perlu:
```bash
php artisan route:clear
php artisan config:clear
```

3. Pastikan user sudah login dan memiliki relasi resident

### 6. Customisasi

- **Interval polling**: Ubah di `status-notifications.js` baris `this.pollInterval = 30000`
- **Durasi toast**: Ubah di `showToast()` method, parameter timeout
- **Styling**: Modifikasi CSS di `status-notifications.js` atau tambahkan custom CSS

### 7. Status yang Ditampilkan

Notifikasi akan muncul untuk perubahan status:
- `delivered` → `in_process`
- `in_process` → `completed`
- `in_process` → `rejected`
- Semua perubahan status lainnya

### 8. Contoh Response API

```json
{
  "count": 3,
  "notifications": [
    {
      "id": 1,
      "report_code": "LPG-2024001",
      "report_title": "Lampu jalan mati",
      "status": "in_process",
      "description": "Laporan sedang diproses",
      "created_at": "2 jam yang lalu",
      "is_read": false
    }
  ]
}
```
