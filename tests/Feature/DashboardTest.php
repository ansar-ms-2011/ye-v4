<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard provides metrics and widgets data for admin user', function () {
    $role = Role::firstOrCreate(['name' => 'admin']);
    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->has('statusBreakdown')
        ->has('exchangeTypeBreakdown')
        ->has('recentApplications')
        ->where('role', 'admin')
    );
});

test('dashboard provides scoped metrics for dyeo and cyeo', function () {
    $role = Role::firstOrCreate(['name' => 'dyeo']);
    $user = User::factory()->create(['district' => '1010']);
    $user->assignRole($role);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->where('role', 'dyeo')
    );
});
