<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Bookings_item;
use App\Models\User;
use App\Models\Period;
use App\Models\Labs;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $period = Period::where('active', true)->first();
        $labs = Labs::all();
        $items = Items::where('condition', true)->get();
        $components = Components::where('condition', true)->get();
        
        if ($users->isEmpty() || !$period || ($labs->isEmpty() && $items->isEmpty() && $components->isEmpty())) {
            return;
        }

        $bookingsData = [];
        
        // Create various booking scenarios - increase to 150 bookings
        for ($i = 1; $i <= 150; $i++) {
            $borrower = $users->random();
            $supervisors = $users->where('email', 'like', '%petra.ac.id')->where('email', 'not like', '%student%');
            $supervisor = $supervisors->isNotEmpty() ? $supervisors->random() : null;
            
            $startDate = now()->addDays(rand(1, 30));
            $endDate = $startDate->copy()->addHours(rand(2, 8));
            
            $booking = [
                'borrower_id' => $borrower->id,
                'supervisor_id' => $supervisor && rand(0, 10) > 7 ? $supervisor->id : null,
                'period_id' => $period->id,
                'event_name' => $this->getRandomEventName(),
                'event_started_at' => $startDate,
                'event_ended_at' => $endDate,
                'borrowed_at' => $startDate->copy()->subHour(),
                'return_deadline_at' => $endDate->copy()->addHour(),
                'phone_number' => '08' . rand(1000000000, 9999999999),
                'booking_detail' => $this->getRandomBookingDetail(),
                'thesis_title' => rand(0, 10) > 7 ? $this->getRandomThesisTitle() : null,
                'attendee_count' => rand(0, 10) > 6 ? rand(5, 30) : null,
                'approved' => $this->getRandomApprovalStatus(),
                'approved_at' => rand(0, 10) > 3 ? now()->subDays(rand(1, 5)) : null,
                'approved_by' => rand(0, 10) > 3 ? $users->random()->id : null,
            ];
            
            $createdBooking = Booking::create($booking);
            
            // Create booking items
            $bookingType = rand(1, 3);
            
            if ($bookingType === 1 && !$labs->isEmpty()) {
                // Lab booking
                $lab = $labs->random();
                Bookings_item::create([
                    'booking_id' => $createdBooking->id,
                    'bookable_type' => 'App\Models\Labs',
                    'bookable_id' => $lab->id,
                    'type' => rand(0, 2),
                    'returned_at' => $this->getReturnedAt($createdBooking),
                    'returned_status' => rand(0, 10) > 8 ? false : true,
                    'returned_detail' => rand(0, 10) > 7 ? 'Returned in good condition' : null,
                    'returner_id' => rand(0, 10) > 5 ? $users->random()->id : null,
                ]);
            } elseif ($bookingType === 2 && !$items->isEmpty()) {
                // Items booking
                $selectedItems = $items->random(rand(1, 3));
                foreach ($selectedItems as $item) {
                    Bookings_item::create([
                        'booking_id' => $createdBooking->id,
                        'bookable_type' => 'App\Models\Items',
                        'bookable_id' => $item->id,
                        'type' => rand(0, 2),
                        'returned_at' => $this->getReturnedAt($createdBooking),
                        'returned_status' => rand(0, 10) > 8 ? false : true,
                        'returned_detail' => rand(0, 10) > 7 ? 'Item returned successfully' : null,
                        'returner_id' => rand(0, 10) > 5 ? $users->random()->id : null,
                    ]);
                }
            } elseif ($bookingType === 3 && !$components->isEmpty()) {
                // Components booking
                $selectedComponents = $components->random(rand(1, 2));
                foreach ($selectedComponents as $component) {
                    Bookings_item::create([
                        'booking_id' => $createdBooking->id,
                        'bookable_type' => 'App\Models\Components',
                        'bookable_id' => $component->id,
                        'type' => rand(0, 2),
                        'returned_at' => $this->getReturnedAt($createdBooking),
                        'returned_status' => rand(0, 10) > 8 ? false : true,
                        'returned_detail' => rand(0, 10) > 7 ? 'Component returned in working condition' : null,
                        'returner_id' => rand(0, 10) > 5 ? $users->random()->id : null,
                    ]);
                }
            }
        }
    }
    
    private function getRandomEventName(): string
    {
        $events = [
            'Praktikum Pemrograman Web',
            'Workshop Machine Learning',
            'Seminar Teknologi Blockchain',
            'Pelatihan Data Science',
            'Penelitian Skripsi',
            'Ujian Praktikum',
            'Presentasi Tugas Akhir',
            'Kelas Pengganti',
            'Meeting Project',
            'Training VR Development'
        ];
        
        return $events[array_rand($events)];
    }
    
    private function getRandomBookingDetail(): string
    {
        $details = [
            'Membutuhkan untuk praktikum mahasiswa semester 5',
            'Digunakan untuk penelitian tugas akhir',
            'Keperluan workshop internal departemen',
            'Backup equipment untuk kelas utama',
            'Testing aplikasi yang sedang dikembangkan',
            'Demonstrasi project kepada client',
            'Pelatihan mahasiswa baru',
            'Pengembangan prototype sistem',
        ];
        
        return $details[array_rand($details)];
    }
    
    private function getRandomThesisTitle(): string
    {
        $titles = [
            'Implementasi Machine Learning untuk Prediksi Cuaca',
            'Pengembangan Aplikasi Mobile E-Commerce',
            'Sistem Informasi Manajemen Inventaris',
            'Analisis Performa Algoritma Sorting',
            'Aplikasi VR untuk Pembelajaran Interaktif',
            'Sistem Keamanan Berbasis Biometrik',
            'Platform IoT untuk Smart Home',
            'Chatbot Customer Service dengan NLP'
        ];
        
        return $titles[array_rand($titles)];
    }
    
    private function getRandomApprovalStatus()
    {
        $rand = rand(0, 10);
        if ($rand > 7) return null; // 30% pending
        if ($rand > 2) return true;  // 50% approved
        return false; // 20% rejected
    }
    
    private function getReturnedAt($booking)
    {
        // 60% chance of being returned
        if (rand(0, 10) > 6) {
            return null;
        }
        
        // Return between borrowed_at and return_deadline_at
        $borrowedAt = $booking->borrowed_at;
        $deadline = $booking->return_deadline_at;
        
        $diffInHours = $borrowedAt->diffInHours($deadline);
        $returnHours = rand(1, max(1, $diffInHours));
        
        return $borrowedAt->copy()->addHours($returnHours);
    }
}