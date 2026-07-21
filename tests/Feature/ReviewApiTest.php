<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ReviewStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Models\SosProvider;
use App\Models\SosRequest;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review_for_workshop_and_rating_cache_updates(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => BookingStatus::Completed,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'workshop_id' => $workshop->id,
            'booking_id' => $booking->id,
            'rating' => 5,
            'quality_rating' => 5,
            'price_rating' => 4,
            'punctuality_rating' => 5,
            'behavior_rating' => 5,
            'comment' => 'Excellent service.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.review.rating', 5)
            ->assertJsonPath('data.review.booking_id', $booking->id);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'booking_id' => $booking->id,
            'rating' => 5,
            'status' => ReviewStatus::Published->value,
        ]);

        $workshop->refresh();
        $this->assertSame(1, $workshop->reviews_count);
        $this->assertSame('5.00', (string) $workshop->rating_avg);
    }

    public function test_my_reviews_returns_only_authenticated_users_reviews(): void
    {
        $user = User::factory()->create();
        $ownReview = Review::factory()->create(['user_id' => $user->id]);
        Review::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/my-reviews')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $ownReview->id);
    }

    public function test_duplicate_review_for_same_booking_is_rejected(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
        ]);
        Review::factory()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'booking_id' => $booking->id,
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'workshop_id' => $workshop->id,
            'booking_id' => $booking->id,
            'rating' => 4,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['booking_id']);
    }

    public function test_booking_and_sos_must_belong_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();
        $booking = Booking::factory()->create([
            'user_id' => $otherUser->id,
            'workshop_id' => $workshop->id,
        ]);
        $sosRequest = SosRequest::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'workshop_id' => $workshop->id,
            'booking_id' => $booking->id,
            'rating' => 4,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['booking_id']);

        $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'workshop_id' => $workshop->id,
            'sos_request_id' => $sosRequest->id,
            'rating' => 4,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sos_request_id']);
    }

    public function test_user_can_update_and_delete_own_review_and_rating_cache_updates(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();
        $ownReview = Review::factory()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'rating' => 3,
        ]);
        Review::factory()->create([
            'workshop_id' => $workshop->id,
            'rating' => 5,
        ]);

        $this->actingAs($user, 'sanctum')->putJson("/api/reviews/{$ownReview->id}", [
            'rating' => 1,
            'comment' => 'Updated.',
        ])
            ->assertOk()
            ->assertJsonPath('data.review.rating', 1);

        $workshop->refresh();
        $this->assertSame(2, $workshop->reviews_count);
        $this->assertSame('3.00', (string) $workshop->rating_avg);

        $this->actingAs($user, 'sanctum')->deleteJson("/api/reviews/{$ownReview->id}")
            ->assertOk();

        $workshop->refresh();
        $this->assertSame(1, $workshop->reviews_count);
        $this->assertSame('5.00', (string) $workshop->rating_avg);
        $this->assertSoftDeleted('reviews', ['id' => $ownReview->id]);
    }

    public function test_user_cannot_update_another_users_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $this->actingAs($user, 'sanctum')->putJson("/api/reviews/{$review->id}", [
            'rating' => 4,
        ])->assertNotFound();
    }

    public function test_review_can_reference_owned_sos_request_for_linked_workshop(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();
        $provider = SosProvider::factory()->create(['workshop_id' => $workshop->id]);
        $sosRequest = SosRequest::factory()->create([
            'user_id' => $user->id,
            'assigned_provider_id' => $provider->id,
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'workshop_id' => $workshop->id,
            'sos_request_id' => $sosRequest->id,
            'rating' => 4,
        ])
            ->assertCreated()
            ->assertJsonPath('data.review.sos_request_id', $sosRequest->id);
    }
}
