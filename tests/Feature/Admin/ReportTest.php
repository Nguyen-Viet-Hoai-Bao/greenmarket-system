<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    /*** ADMIN REPORT ***/

    public function test_admin_can_view_all_reports_page()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.all.reports'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.report.all_report');
    }

    public function test_admin_can_search_reports_by_date()
    {
        $admin = Admin::factory()->create();

        // Tạo order có ngày giống với ngày test
        $orderDate = now()->format('d F Y');
        Order::factory()->create(['order_date' => $orderDate]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.search.bydate'), [
            'date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.report.search_by_date');
        $response->assertViewHas('orderDate');
        $response->assertViewHas('formatDate', $orderDate);
    }

    public function test_admin_can_search_reports_by_month()
    {
        $admin = Admin::factory()->create();

        $month = now()->format('m');
        $year = now()->format('Y');

        Order::factory()->create([
            'order_month' => $month,
            'order_year' => $year,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.search.bymonth'), [
            'month' => $month,
            'year_name' => $year,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.report.search_by_month');
        $response->assertViewHas('orderMonth');
        $response->assertViewHas('month', $month);
        $response->assertViewHas('years', $year);
    }

    public function test_admin_can_search_reports_by_year()
    {
        $admin = Admin::factory()->create();

        $year = now()->format('Y');

        Order::factory()->create(['order_year' => $year]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.search.byyear'), [
            'year' => $year,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.report.search_by_year');
        $response->assertViewHas('orderYear');
        $response->assertViewHas('year', $year);
    }


    /*** CLIENT REPORT ***/

    public function test_client_can_view_all_reports_page()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($client, 'client')->get(route('client.all.reports'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.report.all_report');
    }

    public function test_client_can_search_reports_by_date()
    {
        $client = Client::factory()->create();

        $dateFormatted = now()->format('d F Y');
        $dateInput = now()->format('Y-m-d');

        $order = Order::factory()->create(['order_date' => $dateFormatted]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->post(route('client.search.bydate'), [
            'date' => $dateInput,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.report.search_by_date');
        $response->assertViewHas('orderItemGroupData');
        $response->assertViewHas('formatDate', $dateFormatted);
    }

    public function test_client_can_search_reports_by_month()
    {
        $client = Client::factory()->create();

        $month = now()->format('m');
        $year = now()->format('Y');

        $order = Order::factory()->create([
            'order_month' => $month,
            'order_year' => $year,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->post(route('client.search.bymonth'), [
            'month' => $month,
            'year_name' => $year,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.report.search_by_month');
        $response->assertViewHas('orderItemGroupData');
        $response->assertViewHas('month', $month);
        $response->assertViewHas('years', $year);
    }

    public function test_client_can_search_reports_by_year()
    {
        $client = Client::factory()->create();

        $year = now()->format('Y');

        $order = Order::factory()->create(['order_year' => $year]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($client, 'client')->post(route('client.search.byyear'), [
            'year' => $year,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.report.search_by_year');
        $response->assertViewHas('orderItemGroupData');
        $response->assertViewHas('years', $year);
    }
}
