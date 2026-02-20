<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class PennantFeatureFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_feature_is_enabled_by_default()
    {
        $user = User::factory()->create();
        $this->assertTrue(Feature::for($user)->active('receive-email'));
    }

    public function test_whatsapp_feature_requires_premium_plan()
    {
        $premiumUser = User::factory()->create();
        $premiumUser->plan = 'premium';

        $freeUser = User::factory()->create();
        $freeUser->plan = 'free';

        $this->assertTrue(Feature::for($premiumUser)->active('receive-whatsapp'));
        $this->assertFalse(Feature::for($freeUser)->active('receive-whatsapp'));
    }

    public function test_push_feature_requires_mobile_app_installed()
    {
        $mobileUser = User::factory()->create();
        $mobileUser->has_mobile_app = true;

        $webUser = User::factory()->create();
        $webUser->has_mobile_app = false;

        $this->assertTrue(Feature::for($mobileUser)->active('receive-push'));
        $this->assertFalse(Feature::for($webUser)->active('receive-push'));
    }

    public function test_database_feature_is_enabled()
    {
        $user = User::factory()->create();
        $this->assertTrue(Feature::for($user)->active('receive-database'));
    }

    public function test_sms_feature_is_enabled()
    {
        $user = User::factory()->create();
        $this->assertTrue(Feature::for($user)->active('receive-sms'));
    }
}
