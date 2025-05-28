<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Client;
use App\Models\ProductNew;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ManageOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_view_confirm_orders()
    {
        $client = Client::factory()->create();
        $order = Order::factory()->create(['status' => 'confirm']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->get(route('confirm.order'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.confirm_order');
        $response->assertViewHas('allData');
    }

    public function test_client_can_view_processing_orders()
    {
        $client = Client::factory()->create();
        $order = Order::factory()->create(['status' => 'processing']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->get(route('processing.order'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.processing_order');
        $response->assertViewHas('allData');
    }

    public function test_client_can_view_delivered_orders()
    {
        $client = Client::factory()->create();
        $order = Order::factory()->create(['status' => 'delivered']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->get(route('delivered.order'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.delivered_order');
        $response->assertViewHas('allData');
    }

    public function test_client_can_view_cancel_pending_orders()
    {
        $client = Client::factory()->create();
        $order = Order::factory()->create(['status' => 'cancel_pending']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->get(route('cancel.pending.order'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.cancel_pending_order');
        $response->assertViewHas('allData');
    }

    public function test_client_can_view_cancelled_orders()
    {
        $client = Client::factory()->create();
        $order = Order::factory()->create(['status' => 'cancelled']);
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->get(route('cancelled.order'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.cancelled_order');
        $response->assertViewHas('allData');
    }
        
    public function test_admin_can_view_order_details()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);

        // Tạo product có client liên quan
        $product = ProductNew::factory()->create([
            'client_id' => $client->id,
        ]);

        // Tạo order items với product liên quan
        $orderItems = OrderItem::factory()->count(3)->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'client_id' => $client->id,  // nếu bạn dùng client_id trong OrderItem
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('order.details', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.order.admin_order_details');
        $response->assertViewHasAll(['order', 'orderItem', 'totalPrice', 'totalAmount']);
    }

    public function test_admin_can_update_order_status_to_confirm()
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin, 'admin')->get(route('pening_to_confirm', $order->id));

        $response->assertRedirect(route('confirm.order'));
        $response->assertSessionHas('message', 'Xác nhận đơn hàng thành công');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirm',
        ]);
    }

    public function test_admin_can_update_order_status_to_processing()
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => 'confirm']);

        $response = $this->actingAs($admin, 'admin')->get(route('confirm_to_processing', $order->id));

        $response->assertRedirect(route('processing.order'));
        $response->assertSessionHas('message', 'Đơn hàng đang được xử lý');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_admin_can_update_order_status_to_delivered()
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($admin, 'admin')->get(route('processing_to_delivered', $order->id));

        $response->assertRedirect(route('delivered.order'));
        $response->assertSessionHas('message', 'Đơn hàng đã được giao thành công');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',
        ]);
    }

    public function test_admin_can_view_all_orders()
    {
        $admin = Admin::factory()->create();
        $orderItems = OrderItem::factory()->count(5)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('all.orders'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.order.all_orders');
        $response->assertViewHas('orderItemGroupData');
    }

    public function test_client_can_view_their_order_details()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = ProductNew::factory()->create(['client_id' => $client->id]);
        OrderItem::factory()->count(3)->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get(route('client.order_details', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.order.client_order_details');
        $response->assertViewHasAll([
            'order', 'orderItem', 'totalPrice', 'totalAmount', 'cities', 'menus_footer', 'products_list'
        ]);
    }

    public function test_user_can_view_their_order_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Order::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->get(route('user.order.list'));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.dashboard.order.order_list');
        $response->assertViewHasAll(['allUserOrder', 'cities', 'menus_footer', 'products_list']);
    }

    public function test_user_can_view_their_order_details()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = ProductNew::factory()->create();
        OrderItem::factory()->count(2)->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get(route('user.order.details', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.dashboard.order.order_details');
        $response->assertViewHasAll([
            'order', 'orderItem', 'totalPrice', 'totalAmount', 'cities', 'menus_footer', 'products_list'
        ]);
    }

    public function test_client_can_cancel_order_if_not_delivered()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $order = Order::factory()->create(['status' => 'processing']);  // not delivered

        $response = $this->post(route('client.order.cancel', $order->id), [
            'cancel_reason' => 'Reason test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Đã huỷ đơn hàng thành công.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'cancel_reason' => 'CỬA HÀNG HỦY: Reason test',
        ]);
    }

    public function test_client_cannot_cancel_delivered_order()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $order = Order::factory()->create(['status' => 'delivered']);

        $response = $this->post(route('client.order.cancel', $order->id), [
            'cancel_reason' => 'Cannot cancel delivered',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Không thể huỷ đơn đã giao.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',  // status không đổi
        ]);
    }

    public function test_client_can_cancel_pending_order_directly()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->post(route('client.order.cancel.pending', $order->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Đã huỷ đơn hàng thành công.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

}
