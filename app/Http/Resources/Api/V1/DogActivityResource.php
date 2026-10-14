<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Document;
use App\Models\Feeding;
use App\Models\HealthFlag;
use App\Models\PlaySession;
use App\Models\Schedule;
use App\Models\VetVisit;
use App\Models\Walk;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DogActivityResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = match (get_class($this->resource)) {
            Feeding::class => ['id', 'dog_id', 'fed_at', 'food_type', 'amount_g', 'portion_eaten', 'notes', 'source', 'created_at', 'updated_at'],
            Walk::class => ['id', 'dog_id', 'started_at', 'duration_min', 'distance_km', 'notes', 'source', 'created_at', 'updated_at'],
            PlaySession::class => ['id', 'dog_id', 'played_at', 'activity', 'duration_min', 'energy_level', 'notes', 'source', 'created_at', 'updated_at'],
            VetVisit::class => ['id', 'dog_id', 'visited_on', 'clinic', 'reason', 'diagnosis', 'notes', 'next_due_on', 'created_at', 'updated_at'],
            HealthFlag::class => ['id', 'dog_id', 'flagged_at', 'description', 'severity', 'status', 'created_by', 'resolved_at', 'created_at', 'updated_at'],
            Schedule::class => ['id', 'dog_id', 'type', 'title', 'time_of_day', 'days_of_week', 'is_active', 'created_at', 'updated_at'],
            Document::class => ['id', 'dog_id', 'title', 'type', 'file_path', 'status', 'created_at', 'updated_at'],
            default => [],
        };

        return $this->resource->only($attributes);
    }
}
