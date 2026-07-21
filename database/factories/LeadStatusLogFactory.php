<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadStatusLog>
 */
class LeadStatusLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'old_status' => LeadStatus::New->value,
            'new_status' => LeadStatus::Contacted->value,
            'changed_by' => User::factory(),
        ];
    }
}
