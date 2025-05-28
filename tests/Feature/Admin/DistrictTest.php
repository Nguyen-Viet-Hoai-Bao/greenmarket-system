<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\City;
use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class DistrictTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_districts()
    {
        $admin = Admin::factory()->create();
        $city = City::factory()->create();
        $districts = District::factory()->count(3)->create(['city_id' => $city->id]);

        $response = $this->actingAs($admin, 'admin')->get(route('all.districts', $city->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.city.all_city');
    }

    public function test_admin_can_store_district()
    {
        $admin = Admin::factory()->create();
        $city = City::factory()->create();

        $districtData = [
            'district_name' => 'Test District',
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('district.store', $city->id), $districtData);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Create District Successfully');

        $this->assertDatabaseHas('districts', [
            'city_id' => $city->id,
            'district_name' => 'Test District',
            'district_slug' => Str::slug('Test District'),
        ]);
    }

    public function test_admin_can_get_district_data_for_edit()
    {
        $admin = Admin::factory()->create();
        $city = City::factory()->create();
        $district = District::factory()->create(['city_id' => $city->id]);

        $response = $this->actingAs($admin, 'admin')
            ->get("/edit/district/{$district->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $district->id,
            'district_name' => $district->district_name,
            'district_slug' => $district->district_slug,
            'city_id' => $district->city_id,
        ]);
    }

    public function test_admin_can_update_district()
    {
        $admin = Admin::factory()->create();
        $city = City::factory()->create();
        $district = District::factory()->create(['city_id' => $city->id]);

        $updateData = [
            'cat_id_1' => $district->id,
            'district_name' => 'Updated District Name',
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('district.update'), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Update District Successfully');

        $this->assertDatabaseHas('districts', [
            'id' => $district->id,
            'district_name' => 'Updated District Name',
            'district_slug' => Str::slug('Updated District Name'),
        ]);
    }

    public function test_admin_can_delete_district()
    {
        $admin = Admin::factory()->create();
        $city = City::factory()->create();
        $district = District::factory()->create(['city_id' => $city->id]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('delete.district', $district->id));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Delete District Successfully');

        $this->assertDatabaseMissing('districts', ['id' => $district->id]);
    }
}
