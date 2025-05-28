<?php

namespace Tests\Feature\Client;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Client::factory()->create();
        $this->actingAs($this->client, 'client');
    }

    public function test_all_coupons_view()
    {
        Coupon::factory()->count(3)->create(['client_id' => $this->client->id]);

        $response = $this->get(route('all.coupon'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.coupon.all_coupon');
        $response->assertViewHas('coupon');
    }

    public function test_add_coupon_view()
    {
        $response = $this->get(route('add.coupon'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.coupon.add_coupon');
    }

    public function test_store_coupon()
    {
        $data = [
            'coupon_name' => 'testcoupon',
            'coupon_desc' => 'Test coupon description',
            'discount' => 15,
            'validity' => Carbon::now()->addDays(10)->toDateString(),
        ];

        $response = $this->post(route('coupon.store'), $data);

        $response->assertRedirect(route('all.coupon'));
        $response->assertSessionHas('message', 'Insert Coupon Successfully');

        $this->assertDatabaseHas('coupons', [
            'coupon_name' => strtoupper($data['coupon_name']),
            'coupon_desc' => $data['coupon_desc'],
            'discount' => $data['discount'],
            'client_id' => $this->client->id,
        ]);
    }

    public function test_edit_coupon_view()
    {
        $coupon = Coupon::factory()->create(['client_id' => $this->client->id]);

        $response = $this->get(route('edit.coupon', $coupon->id));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.coupon.edit_coupon');
        $response->assertViewHas('coupon', $coupon);
    }

    public function test_update_coupon()
    {
        $coupon = Coupon::factory()->create(['client_id' => $this->client->id]);

        $data = [
            'id' => $coupon->id,
            'coupon_name' => 'updatedcoupon',
            'coupon_desc' => 'Updated description',
            'discount' => 20,
            'validity' => Carbon::now()->addDays(20)->toDateString(),
        ];

        $response = $this->post(route('coupon.update'), $data);

        $response->assertRedirect(route('all.coupon'));
        $response->assertSessionHas('message', 'Update Coupon Successfully');

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'coupon_name' => strtoupper($data['coupon_name']),
            'coupon_desc' => $data['coupon_desc'],
            'discount' => $data['discount'],
        ]);
    }

    public function test_delete_coupon()
    {
        $coupon = Coupon::factory()->create(['client_id' => $this->client->id]);

        $response = $this->get(route('delete.coupon', $coupon->id));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Delete Coupon Successfully');

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}
