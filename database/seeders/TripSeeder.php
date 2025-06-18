<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 20) as $i) {
            $startTime = Carbon::now()->subDays(rand(1, 10))->setTime(rand(7, 10), rand(0, 59));
            $endTime = (clone $startTime)->addMinutes(rand(30, 180));

            $approvalStatus = collect(['pending', 'approved', 'rejected'])->random();
            $approvedBy = ($approvalStatus !== 'pending') ? 2 : null; // Assuming manager has user_id 2
            $approvalReason = $approvalStatus === 'rejected' ? 'Insufficient details provided' : null;
            $approvedAt = ($approvalStatus !== 'pending') ? Carbon::now()->subDays(rand(0, 5)) : null;

            DB::table('trips')->insert([
                'user_id' => 1, // Hardcoded user_id for simplicity
                'trip_date' => $startTime->toDateString(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'start_lat' => 23.0225 + rand(-50, 50) / 1000,
                'start_lng' => 72.5714 + rand(-50, 50) / 1000,
                'end_lat' => 23.0225 + rand(-50, 50) / 1000,
                'end_lng' => 72.5714 + rand(-50, 50) / 1000,
                'total_distance_km' => rand(1, 50) + rand(0, 99) / 100,
                'travel_mode' => 'car',
                'purpose' => 'Routine field visit',
                'status' => 'completed',

                'approval_status' => $approvalStatus,
                'approval_reason' => $approvalReason,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
