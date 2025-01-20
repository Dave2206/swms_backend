<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert 10 sample announcements into the announcements table
        DB::table('announcements')->insert([
            [
                'subject' => 'Waste Collection Schedule Update',
                'effective_date' => '2025-01-15',
                'route' => 'City Center, Downtown',
                'body' => 'Due to maintenance work, the waste collection schedule for City Center and Downtown will be updated starting from January 15, 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Holiday Schedule - No Waste Collection',
                'effective_date' => '2025-12-25',
                'route' => 'All Areas',
                'body' => 'Please be advised that there will be no waste collection on Christmas Day, December 25, 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'New Recycling Guidelines',
                'effective_date' => '2025-02-01',
                'route' => 'All Areas',
                'body' => 'Starting February 1, 2025, please follow the new guidelines for recycling. Make sure to separate paper, plastic, and glass.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Annual Clean-Up Drive',
                'effective_date' => '2025-03-01',
                'route' => 'All Areas',
                'body' => 'Join us on March 1, 2025, for the annual clean-up drive. Volunteers are welcome!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'New Garbage Collection Hours',
                'effective_date' => '2025-05-10',
                'route' => 'City Center',
                'body' => 'Please be advised that the garbage collection hours will be changed starting May 10, 2025. Collection will now begin at 6:00 AM.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Extended Waste Disposal Service',
                'effective_date' => '2025-07-01',
                'route' => 'Downtown Area',
                'body' => 'Our waste disposal service has been extended to the Downtown Area from July 1, 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Street Sweeping Service Delay',
                'effective_date' => '2025-08-05',
                'route' => 'Residential Areas',
                'body' => 'Due to unforeseen circumstances, street sweeping services in residential areas will be delayed on August 5, 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Recycling Center Closure',
                'effective_date' => '2025-09-20',
                'route' => 'Main Recycling Center',
                'body' => 'The Main Recycling Center will be closed for maintenance on September 20, 2025. We apologize for the inconvenience.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Waste Management Fee Adjustment',
                'effective_date' => '2025-10-01',
                'route' => 'All Areas',
                'body' => 'Starting October 1, 2025, there will be a minor adjustment in waste management fees.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subject' => 'Plastic Waste Ban',
                'effective_date' => '2025-11-15',
                'route' => 'All Areas',
                'body' => 'A ban on plastic waste will be implemented starting November 15, 2025. Please switch to alternative materials.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
