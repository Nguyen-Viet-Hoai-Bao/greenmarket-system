<?php

use App\Models\Admin;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('admin can view all cities', function () {
    $admin = Admin::factory()->create();
    City::factory()->count(3)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('all.city'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.backend.city.all_city');
    $response->assertViewHas('city');
    expect($response->viewData('city')->count())->toBe(3);
});

test('admin can store a new city', function () {
    $admin = Admin::factory()->create();

    $postData = [
        'city_name' => 'Hanoi',
    ];

    $response = $this->actingAs($admin, 'admin')
        ->post(route('city.store'), $postData);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Create City Successfully');

    $this->assertDatabaseHas('cities', [
        'city_name' => 'Hanoi',
        'city_slug' => Str::slug('Hanoi'),
    ]);
});

test('admin can get city data for edit', function () {
    $admin = Admin::factory()->create();
    $city = City::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get("/edit/city/{$city->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $city->id,
        'city_name' => $city->city_name,
        'city_slug' => $city->city_slug,
    ]);
});

test('admin can update city', function () {
    $admin = Admin::factory()->create();
    $city = City::factory()->create([
        'city_name' => 'Old Name',
        'city_slug' => Str::slug('Old Name'),
    ]);

    $postData = [
        'cat_id' => $city->id,
        'city_name' => 'New City Name',
    ];

    $response = $this->actingAs($admin, 'admin')
        ->post(route('city.update'), $postData);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Update City Successfully');

    $this->assertDatabaseHas('cities', [
        'id' => $city->id,
        'city_name' => 'New City Name',
        'city_slug' => Str::slug('New City Name'),
    ]);
});

test('admin can delete city', function () {
    $admin = Admin::factory()->create();
    $city = City::factory()->create();

    $response = $this->actingAs($admin, 'admin')
        ->get(route('delete.city', $city->id));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Delete City Successfully');

    $this->assertDatabaseMissing('cities', [
        'id' => $city->id,
    ]);
});

