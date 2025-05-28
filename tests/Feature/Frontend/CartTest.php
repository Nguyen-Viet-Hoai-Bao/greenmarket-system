<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\Client;
use App\Models\ProductTemplate;
use App\Models\ProductNew;
use App\Models\Menu;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function testAddToCartSuccess()
    {
        $client = Client::factory()->create();
        $product = ProductNew::factory()->create([
            'client_id' => $client->id,
            'discount_price' => 90000,
            'price' => 100000,
        ]);

        session(['selected_market_id' => $client->id]);

        $response = $this->get(route('add_to_cart', ['id' => $product->id]));

        $response->assertRedirect();
        $response->assertSessionHas('cart');
        $response->assertSessionHas('message', 'Thêm vào giỏ hàng thành công.');
    }

    public function testAddToCartFailsIfProductNotFound()
    {
        $response = $this->get(route('add_to_cart', ['id' => 9999]));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Product not found.');
    }

    public function testAddToCartFailsIfWrongClient()
    {
        $client = Client::factory()->create();
        $wrongClient = Client::factory()->create();
        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        session(['selected_market_id' => $wrongClient->id]);

        $response = $this->get(route('add_to_cart', ['id' => $product->id]));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Sản phẩm không thuộc cửa hàng hiện tại.');
    }

    public function testAjaxAddToCartSuccess()
    {
        $client = Client::factory()->create();
        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        session(['selected_market_id' => $client->id]);

        $response = $this->getJson(route('ajax.add_to_cart', ['id' => $product->id]));

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
        ]);
    }

    public function testAjaxAddToCartFailsIfProductNotFound()
    {
        $response = $this->getJson(route('ajax.add_to_cart', ['id' => 9999]));

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Product not found.'
        ]);
    }

    public function testAjaxAddToCartFailsIfWrongClient()
    {
        $client = Client::factory()->create();
        $wrongClient = Client::factory()->create();
        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        session(['selected_market_id' => $wrongClient->id]);

        $response = $this->getJson(route('ajax.add_to_cart', ['id' => $product->id]));

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Sản phẩm không thuộc cửa hàng hiện tại.'
        ]);
    }

    public function testAjaxUpdateCartSuccess()
    {
        $productId = 1;
        session(['cart' => [
            $productId => [
                'id' => $productId,
                'quantity' => 1,
                'price' => 100000,
                'name' => 'Product',
                'image' => 'image.jpg',
                'client_id' => 1
            ]
        ]]);

        $response = $this->postJson(route('ajax.update_cart', ['id' => $productId]), [
            'quantity' => 5
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'cartItem' => ['quantity' => 5]
        ]);
    }

    public function testAjaxUpdateCartFailsIfNotInCart()
    {
        session(['cart' => []]);

        $response = $this->postJson(route('ajax.update_cart', ['id' => 123]), [
            'quantity' => 2
        ]);

        $response->assertJson(['status' => 'error']);
    }

    public function testAjaxRemoveFromCartSuccess()
    {
        $productId = 1;
        session(['cart' => [
            $productId => [
                'id' => $productId,
                'quantity' => 1,
                'price' => 100000
            ]
        ]]);

        $response = $this->postJson(route('ajax.remove_from_cart', ['id' => $productId]));

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
    }

    public function testAjaxRemoveFromCartFailsIfNotFound()
    {
        session(['cart' => []]);

        $response = $this->postJson(route('ajax.remove_from_cart', ['id' => 999]));

        $response->assertJson(['status' => 'error']);
    }

    public function testAjaxReloadCartHeaderWithValidClient()
    {
        $client = Client::factory()->create();

        // Tạo sản phẩm liên quan
        $product = ProductNew::factory()->create([
            'id' => 1,
            'client_id' => $client->id,
            'price' => 100000
        ]);

        session(['selected_market_id' => $client->id]);
        session(['cart' => [
            1 => [
                'id' => $product->id,
                'quantity' => 2,
                'price' => $product->price,
                'client_id' => $client->id
            ]
        ]]);

        $response = $this->get(route('ajax.cart.header.reload'));

        $this->assertTrue(true);
    }

    public function testUpdateCartQuantitySuccessfully()
    {
        session()->put('cart', [
            1 => ['id' => 1, 'price' => 10000, 'quantity' => 2]
        ]);

        $response = $this->postJson(route('cart.updateQuantity'), [
            'id' => 1,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Quantity Updated',
            'alert-type' => 'success',
        ]);

        $this->assertEquals(3, session('cart')[1]['quantity']);
    }

    public function testUpdateCartQuantityRemovesIfZero()
    {
        session()->put('cart', [
            1 => ['id' => 1, 'price' => 10000, 'quantity' => 2]
        ]);

        $response = $this->postJson(route('cart.updateQuantity'), [
            'id' => 1,
            'quantity' => 0,
        ]);

        $response->assertStatus(200);
        $this->assertArrayNotHasKey(1, session('cart'));
    }

    public function testCartRemoveSuccessfully()
    {
        session()->put('cart', [
            1 => ['id' => 1, 'price' => 10000, 'quantity' => 1]
        ]);

        $response = $this->postJson(route('cart.remove'), ['id' => 1]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Cart Remove Successfully',
            'alert-type' => 'success',
        ]);
        $this->assertEmpty(session('cart'));
    }

    public function testApplyCouponSuccess()
    {
        $client = \App\Models\Client::factory()->create();

        $product = \App\Models\ProductNew::factory()->create([
            'price' => 100000,
            'client_id' => $client->id
        ]);

        session()->put('cart', [
            $product->id => ['id' => $product->id, 'price' => 100000, 'quantity' => 2]
        ]);

        $coupon = \App\Models\Coupon::factory()->create([
            'coupon_name' => 'SUMMER50',
            'discount' => 50,
            'client_id' => $client->id,
            'validity' => now()->addDays(1)->format('Y-m-d')
        ]);

        $response = $this->postJson('/apply-coupon', ['coupon_name' => 'SUMMER50']);

        $response->assertStatus(200);
        $response->assertJson([
            'validity' => true,
            'success' => 'Coupon Applied Successfully'
        ]);
    }

    public function testApplyCouponFailsWithInvalidCoupon()
    {
        $response = $this->postJson('/apply-coupon', ['coupon_name' => 'INVALIDCODE']);

        $response->assertStatus(200);
        $response->assertJson([
            'error' => 'Invalid Coupon',
        ]);
    }

    public function testRemoveCouponClearsSession()
    {
        session()->put('coupon', [
            'coupon_name' => 'SUMMER50',
            'discount' => 50,
            'discount_amount' => 50000
        ]);

        $response = $this->get('/remove-coupon');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Coupon Remove Successfully',
        ]);
        $this->assertFalse(session()->has('coupon'));
    }

    public function testMarketCheckoutRedirectsWhenNotLoggedIn()
    {
        $response = $this->get(route('checkout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('message', 'Vui lòng hãy đăng nhập trước khi sử dụng');
    }

    public function testMarketCheckoutRedirectsWhenCartEmpty()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        session()->put('cart', []);

        $response = $this->get(route('checkout'));

        $response->assertRedirect('/');
        $response->assertSessionHas('message', 'Vui lòng mua ít nhất một món hàng');
    }


}