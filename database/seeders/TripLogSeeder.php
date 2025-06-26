<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripLog;
use Carbon\Carbon;

class TripLogSeeder extends Seeder
{
    public function run()
    {
        // Array of trips with their coordinates
        $routes = [
            [
                'trip_id'  => 1,
                'from_lat' => 23.0225, 'from_lng' => 72.5714, // Ahmedabad
                'to_lat'   => 22.7272, 'to_lng'   => 71.6379, // Surendranagar
            ],
            [
                'trip_id'  => 2,
                'from_lat' => 23.0225, 'from_lng' => 72.5714, // Ahmedabad
                'to_lat'   => 22.3039, 'to_lng'   => 70.8022, // Rajkot
            ],
            [
                'trip_id'  => 3,
                'from_lat' => 22.3039, 'from_lng' => 70.8022, // Rajkot
                'to_lat'   => 21.7645, 'to_lng'   => 72.1519, // Bhavnagar
            ],
            [
                'trip_id'  => 4,
                'from_lat' => 21.7645, 'from_lng' => 72.1519, // Bhavnagar
                'to_lat'   => 21.1702, 'to_lng'   => 72.8311, // Surat
            ],
            [
                'trip_id'  => 5,
                'from_lat' => 23.0225, 'from_lng' => 72.5714, // Ahmedabad
                'to_lat'   => 21.1702, 'to_lng'   => 72.8311, // Surat
            ],
        ];

        $numLogs = 100;
        $baseTimestamp = Carbon::now();

        foreach ($routes as $index => $route) {
            for ($i = 0; $i < $numLogs; $i++) {
                $fraction = $i / ($numLogs - 1);

                $lat = $route['from_lat'] + ($route['to_lat'] - $route['from_lat']) * $fraction;
                $lng = $route['from_lng'] + ($route['to_lng'] - $route['from_lng']) * $fraction;

                TripLog::create([
                    'trip_id'     => $route['trip_id'],
                    'latitude'    => $lat,
                    'longitude'   => $lng,
                    'recorded_at' => $baseTimestamp->copy()->addSeconds($index * 3600 + $i * 10),
                ]);
            }
        }
    }
}
