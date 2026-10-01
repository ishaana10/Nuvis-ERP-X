<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use Tests\TestCase;
use Webkul\Security\Models\User;

class MainDashboardTest extends TestCase
{
    public function test_main_dashboard_renders_successfully(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->get(Dashboard::getUrl());
        $response->assertSuccessful();
    }
}
