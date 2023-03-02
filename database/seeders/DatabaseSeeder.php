<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\ConsultationAnnouncementCategories;
use App\Enums\ConsultationAppointmentTimesStatus;
use App\Models\ConsultationAnnouncement;
use App\Models\Professional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();

        $user = \App\Models\User::factory()->create([
            'name' => 'Test Professional User',
            'password' => Hash::make('password'),
        ]);

        Professional::factory(1)->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'phone_number' => fake()->numberBetween(11111111110, 99999999999),
            'crm' => fake()->numberBetween(100, 5000),
            'photo_url' => fake()->imageUrl(width: 200, height: 200, word: 'Test Professional User'),
        ]);

        $announcements = ConsultationAnnouncement::factory(10)->create([
            'professional_id' => $user->id,
            'category' => fake()->randomElement(ConsultationAnnouncementCategories::casesAsArrayString()),
            'description' => fake()->text()
        ]);

        foreach ($announcements as $announcement) {
            $announcement->appointmentTimes()->createMany([
                [
                    'start' => (new \DateTime()),
                    'end' => (new \DateTime())->modify('+1 hour'),
                    'price' => fake()->randomFloat(2, 5, 1000),
                    'status' => ConsultationAppointmentTimesStatus::Available->value
                ],
                [
                    'start' => (new \DateTime())->modify('+2 hour'),
                    'end' => (new \DateTime())->modify('+3 hour'),
                    'price' => fake()->randomFloat(2, 5, 1000),
                    'status' => ConsultationAppointmentTimesStatus::Available->value
                ],
                [
                    'start' => (new \DateTime())->modify('+5 hour'),
                    'end' => (new \DateTime())->modify('+6 hour'),
                    'price' => fake()->randomFloat(2, 5, 1000),
                    'status' => ConsultationAppointmentTimesStatus::Available->value
                ],
            ]);
        }
    }
}
