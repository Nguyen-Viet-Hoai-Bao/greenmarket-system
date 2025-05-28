<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Ward;
use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class WardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_all_wards_view()
    {
        // Tạo dữ liệu test
        $districts = District::factory()->count(2)->create();
        Ward::factory()->count(3)->create(['district_id' => $districts[0]->id]);

        $response = $this->get(route('all.wards', ['districtId' => $districts[0]->id]));

        $response->assertStatus(200);
    }

    public function test_create_ward_store()
    {
        $district = District::factory()->create();

        $response = $this->post(route('store.ward', ['districtId' => $district->id]), [
            'ward_name' => 'Ward Test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Create Ward Successfully');

        $this->assertDatabaseHas('wards', [
            'district_id' => $district->id,
            'ward_name' => 'Ward Test',
            'ward_slug' => Str::slug('Ward Test'),
        ]);
    }

    public function test_edit_ward_returns_json()
    {
        $ward = Ward::factory()->create();

        $response = $this->get('/edit/ward/' . $ward->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $ward->id,
            'ward_name' => $ward->ward_name,
            'district_id' => $ward->district_id,
        ]);
    }

    public function test_update_ward()
    {
        $ward = Ward::factory()->create();

        $response = $this->post(route('ward.update'), [
            'cat_id_2' => $ward->id,
            'ward_name' => 'Ward Updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Update Ward Successfully');

        $this->assertDatabaseHas('wards', [
            'id' => $ward->id,
            'ward_name' => 'Ward Updated',
            'ward_slug' => Str::slug('Ward Updated'),
        ]);
    }

    public function test_delete_ward()
    {
        $ward = Ward::factory()->create();

        $response = $this->get(route('delete.ward', $ward->id));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Delete Ward Successfully');

        $this->assertDatabaseMissing('wards', ['id' => $ward->id]);
    }
}
