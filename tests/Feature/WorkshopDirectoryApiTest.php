<?php

namespace Tests\Feature;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\CarBrand;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopDirectoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_workshop_index_returns_approved_filtered_workshops_with_relations(): void
    {
        $brand = CarBrand::factory()->create();
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create(['service_category_id' => $category->id]);
        $approved = Workshop::factory()->approved()->create([
            'name' => 'Cairo Engine Care',
            'city' => 'Cairo',
            'area' => 'Nasr City',
            'rating_avg' => 4.5,
            'is_verified' => true,
            'accepts_booking' => true,
        ]);
        $approved->services()->sync([$service->id]);
        $approved->brands()->sync([$brand->id]);
        Workshop::factory()->create(['name' => 'Pending Engine Care']);

        $response = $this->getJson("/api/workshops?search=Engine&service_id={$service->id}&brand_id={$brand->id}&city=Cairo&rating=4&is_verified=1&accepts_booking=1");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Cairo Engine Care')
            ->assertJsonStructure(['data' => ['items' => [['services', 'brands', 'images', 'rating_avg']]]]);
    }

    public function test_nearby_workshops_use_distance_and_default_radius(): void
    {
        Workshop::factory()->approved()->create([
            'name' => 'Near Workshop',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'rating_avg' => 4.0,
        ]);
        Workshop::factory()->approved()->create([
            'name' => 'Far Workshop',
            'latitude' => 31.2001,
            'longitude' => 29.9187,
            'rating_avg' => 5.0,
        ]);

        $response = $this->getJson('/api/workshops/nearby?lat=30.0444&lng=31.2357');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Near Workshop');
    }

    public function test_authenticated_workshop_detail_view_tracks_profile_view(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/workshops/{$workshop->id}")
            ->assertOk()
            ->assertJsonPath('data.workshop.id', $workshop->id);

        $this->assertDatabaseHas('workshop_analytics_events', [
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'event_type' => WorkshopAnalyticsEventType::ProfileView->value,
        ]);
    }

    public function test_workshop_services_and_published_reviews_are_returned(): void
    {
        $workshop = Workshop::factory()->approved()->create();
        $service = Service::factory()->create(['name' => 'Oil Change', 'slug' => 'oil-change']);
        $workshop->services()->sync([$service->id]);
        Review::factory()->create(['workshop_id' => $workshop->id, 'rating' => 5]);
        Review::factory()->hidden()->create(['workshop_id' => $workshop->id, 'rating' => 1]);

        $this->getJson("/api/workshops/{$workshop->id}/services")
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Oil Change');

        $this->getJson("/api/workshops/{$workshop->id}/reviews")
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.rating', 5);
    }
}
