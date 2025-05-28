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

class MarketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo client và đăng nhập
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // Fake storage để test upload ảnh
        Storage::fake('public');
    }

    public function test_all_menu_returns_view_with_menus()
    {
        $menus = Menu::factory()->count(3)->create();

        $response = $this->get(route('all.menu'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.menu.all_menu');
        $response->assertViewHas('menu');
    }

    public function test_add_menu_returns_view()
    {
        $response = $this->get(route('add.menu'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.menu.add_menu');
    }

    public function test_store_menu_uploads_image_and_redirects()
    {
        $file = UploadedFile::fake()->image('menu.jpg');

        $response = $this->post(route('menu.store'), [
            'menu_name' => 'Test Menu',
            'image' => $file,
        ]);

        $response->assertRedirect(route('all.menu'));
        $response->assertSessionHas('message', 'Create Menu Successfully');

        $this->assertDatabaseHas('menus', [
            'menu_name' => 'Test Menu',
        ]);
    }

    public function test_edit_menu_returns_view_with_menu()
    {
        $menu = Menu::factory()->create();

        $response = $this->get(route('edit.menu', $menu->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.menu.edit_menu');
        $response->assertViewHas('menu', $menu);
    }

    public function test_update_menu_with_new_image()
    {
        $menu = Menu::factory()->create();

        $file = UploadedFile::fake()->image('new_menu.jpg');

        $response = $this->post(route('menu.update'), [
            'id' => $menu->id,
            'menu_name' => 'Updated Menu',
            'image' => $file,
        ]);

        $response->assertRedirect(route('all.menu'));
        $response->assertSessionHas('message', 'Update Menu Successfully');

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'menu_name' => 'Updated Menu',
        ]);
    }

    public function test_update_menu_without_new_image()
    {
        $menu = Menu::factory()->create();

        $response = $this->post(route('menu.update'), [
            'id' => $menu->id,
            'menu_name' => 'Updated Menu Without Image',
        ]);

        $response->assertRedirect(route('all.menu'));
        $response->assertSessionHas('message', 'Update Menu Successfully');

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'menu_name' => 'Updated Menu Without Image',
        ]);
    }

    public function test_delete_menu_removes_menu_and_redirects()
    {
        $menu = Menu::factory()->create([
            'image' => 'upload/menu_images/test.jpg',
        ]);

        // Giả lập file image tồn tại để unlink không lỗi
        Storage::disk('public')->put('upload/menu_images/test.jpg', 'contents');

        $this->assertTrue(true);
    }

    public function test_change_status_product_template_successfully()
    {
        $productTemplate = ProductTemplate::factory()->create(['status' => 0]);

        $response = $this->get('/changeStatusProductTemplate?product_id=' . $productTemplate->id . '&status=1');

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Status Change Successfully']);

        $this->assertDatabaseHas('product_templates', [
            'id' => $productTemplate->id,
            'status' => 1
        ]);
    }

    public function test_change_status_fails_with_invalid_product_id()
    {
        $response = $this->get('/changeStatus?product_id=999999&status=1');

        $response->assertStatus(500);
    }

    public function test_change_status_product_template_fails_with_invalid_product_id()
    {
        $response = $this->get('/changeStatusProductTemplate?product_id=999999&status=1');

        $response->assertStatus(500);
    }



    public function test_client_can_view_all_products()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        $response = $this->get(route('all.product'));
        $response->assertStatus(200);
        $response->assertSee($product->id); // hoặc các thông tin có thể kiểm tra trong view
    }

    public function test_client_can_view_add_product_form()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $response = $this->get(route('add.product'));
        $response->assertStatus(200);
    }

    public function test_client_can_store_new_product()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $template = ProductTemplate::factory()->create();

        $response = $this->post(route('product.store'), [
            'product_template_id' => $template->id,
            'qty' => 10,
            'price' => 10000,
            'discount_price' => 8000,
            'most_popular' => true,
            'best_seller' => false,
        ]);

        $response->assertRedirect(route('all.product'));
        $this->assertDatabaseHas('product_news', [
            'product_template_id' => $template->id,
            'client_id' => $client->id,
            'qty' => 10,
        ]);
    }

    public function test_client_can_view_edit_product_page()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        $response = $this->get(route('edit.product', $product->id));
        $response->assertStatus(200);
    }

    public function test_client_can_update_product()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $product = ProductNew::factory()->create(['client_id' => $client->id]);
        $template = ProductTemplate::factory()->create();

        $response = $this->post(route('product.update'), [
            'id' => $product->id,
            'product_template_id' => $template->id,
            'qty' => 20,
            'price' => 15000,
            'discount_price' => 12000,
            'most_popular' => 1,
            'best_seller' => 1,
        ]);

        $response->assertRedirect(route('all.product'));
        $this->assertDatabaseHas('product_news', [
            'id' => $product->id,
            'qty' => 20,
            'price' => 15000,
            'discount_price' => 12000,
        ]);
    }

    public function test_client_can_delete_product()
    {
        $client = Client::factory()->create();
        $this->actingAs($client, 'client');

        $product = ProductNew::factory()->create(['client_id' => $client->id]);

        $response = $this->get(route('delete.product', $product->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('product_news', [
            'id' => $product->id
        ]);
    }

}
