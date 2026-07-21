<?php

namespace Tests\Feature;

use App\Enums\DevicePlatform;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_multiple_device_tokens(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/device-tokens', [
            'token' => 'token-one',
            'platform' => DevicePlatform::Android->value,
            'device_name' => 'Pixel',
        ])
            ->assertCreated()
            ->assertJsonPath('data.device_token.token', 'token-one')
            ->assertJsonPath('data.device_token.platform', DevicePlatform::Android->value);

        $this->actingAs($user, 'sanctum')->postJson('/api/device-tokens', [
            'token' => 'token-two',
            'platform' => DevicePlatform::Ios->value,
            'device_name' => 'iPhone',
        ])
            ->assertCreated()
            ->assertJsonPath('data.device_token.token', 'token-two');

        $this->assertSame(2, DeviceToken::query()->where('user_id', $user->id)->count());
    }

    public function test_storing_same_device_token_updates_device_info(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/device-tokens', [
            'token' => 'same-token',
            'platform' => DevicePlatform::Android->value,
            'device_name' => 'Old',
        ])->assertCreated();

        $this->actingAs($user, 'sanctum')->postJson('/api/device-tokens', [
            'token' => 'same-token',
            'platform' => DevicePlatform::Web->value,
            'device_name' => 'Browser',
        ])
            ->assertCreated()
            ->assertJsonPath('data.device_token.platform', DevicePlatform::Web->value)
            ->assertJsonPath('data.device_token.device_name', 'Browser');

        $this->assertSame(1, DeviceToken::query()->where('user_id', $user->id)->where('token', 'same-token')->count());
    }

    public function test_user_can_list_only_own_notifications(): void
    {
        $user = User::factory()->create();
        $own = Notification::factory()->create(['user_id' => $user->id, 'title' => 'Own']);
        Notification::factory()->create(['title' => 'Other']);

        $this->actingAs($user, 'sanctum')->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $own->id)
            ->assertJsonPath('data.items.0.is_read', false);
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')->putJson("/api/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('data.notification.is_read', true);

        $this->assertNotNull($notification->refresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create();

        $this->actingAs($user, 'sanctum')->putJson("/api/notifications/{$notification->id}/read")
            ->assertNotFound();
    }

    public function test_user_can_mark_all_own_notifications_as_read(): void
    {
        $user = User::factory()->create();
        Notification::factory()->count(2)->create(['user_id' => $user->id]);
        Notification::factory()->read()->create(['user_id' => $user->id]);
        Notification::factory()->create();

        $this->actingAs($user, 'sanctum')->putJson('/api/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', 2);

        $this->assertSame(0, Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count());
    }
}
