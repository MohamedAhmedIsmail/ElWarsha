<?php

namespace Database\Factories;

use App\Enums\SosRequestStatus;
use App\Models\SosRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SosRequestLog>
 */
class SosRequestLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sos_request_id' => SosRequest::factory(),
            'old_status' => null,
            'new_status' => SosRequestStatus::Pending->value,
            'changed_by' => null,
            'notes' => null,
        ];
    }
}
