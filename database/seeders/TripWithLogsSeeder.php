<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TripWithLogsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::first(); // 👤 Assign first user as owner

            if (!$user) {
                $this->command->error('No users found. Please seed users first.');
                return;
            }

            $baseLat = 28.6139; // New Delhi approx
            $baseLng = 77.2090;

            for ($i = 1; $i <= 5; $i++) {
                $trip = Trip::create([
                    'user_id'           => $user->id,
                    'company_id'        => $user->company_id ?? 1,
                    'trip_date'         => now()->subDays($i)->toDateString(),
                    'start_time'        => '09:00:00',
                    'end_time'          => '10:00:00',
                    'start_lat'         => $baseLat,
                    'start_lng'         => $baseLng,
                    'end_lat'           => $baseLat + 0.09, // ~10km
                    'end_lng'           => $baseLng + 0.09, // ~10km
                    'total_distance_km' => 10.00,
                    'travel_mode'       => 'car',
                    'purpose'           => 'Seeded trip demo',
                    'status'            => 'completed',
                    'approval_status'   => 'approved',
                    'approved_by'       => $user->id,
                    'approved_at'       => now()
                ]);

                // Create 25 points (roughly 400m spacing for 10km)
                $points = 25;
                for ($j = 0; $j <= $points; $j++) {
                    $fraction = $j / $points;

                    TripLog::create([
                        'trip_id'     => $trip->id,
                        'latitude'    => $trip->start_lat + ($trip->end_lat - $trip->start_lat) * $fraction,
                        'longitude'   => $trip->start_lng + ($trip->end_lng - $trip->start_lng) * $fraction,
                        'recorded_at' => now()->subDays($i)->addMinutes($j * 2),
                    ]);
                }

                $this->command->info("Created trip #$i with 25 GPS logs.");
            }
        });
    }
}
