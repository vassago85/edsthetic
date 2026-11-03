<?php

namespace Database\Factories;

use App\Enums\EnquiryStatus;
use App\Enums\JobType;
use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    protected $model = Enquiry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'job_type' => fake()->randomElement(JobType::cases()),
            'message' => fake()->sentence(),
            'status' => EnquiryStatus::New,
            'internal_notes' => null,
            'read_at' => null,
            'ip_address' => fake()->ipv4(),
        ];
    }
}
