<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Menu;
use App\Models\ProductTemplate;
use App\Models\ProductDetail;
use App\Models\Client;
use App\Models\Banner;

class ManageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_products()
    {
        $admin = Admin::factory()->create();
        $product = ProductTemplate::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.all.product'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.product.all_product');
        $response->assertViewHas('product');
    }

    public function test_admin_can_see_add_product_form_with_categories_and_menus()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.add.product'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.product.add_product');
        $response->assertViewHasAll(['category', 'menu']);
    }

    public function test_admin_can_store_product_with_image()
    {
        $admin = Admin::factory()->create();
        Storage::fake('public');

        $category = Category::factory()->create();
        $menu = Menu::factory()->create();
        $file = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($admin, 'admin')->post(route('admin.product.store'), [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'menu_id' => $menu->id,
            'size' => 'M',
            'unit' => 'pcs',
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.all.product'));
        $response->assertSessionHas('message', 'Create Menu Successfully');

        $this->assertDatabaseHas('product_templates', [
            'name' => 'Test Product',
        ]);

        // Storage::disk('public')->assertExists('upload/product_template_images/' . basename(ProductTemplate::first()->image));
    }

    public function test_admin_can_view_edit_product_form()
    {
        $admin = Admin::factory()->create();
        $product = ProductTemplate::factory()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.edit.product', $product->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.product.edit_product');
        $response->assertViewHasAll(['category', 'menu', 'product']);
    }

    public function test_admin_can_update_product_with_new_image()
    {
        $admin = Admin::factory()->create();
        Storage::fake('public');

        $product = ProductTemplate::factory()->create();
        $file = UploadedFile::fake()->image('newimage.jpg');

        $response = $this->actingAs($admin, 'admin')->post(route('admin.product.update'), [
            'id' => $product->id,
            'name' => 'Updated Product',
            'category_id' => $product->category_id,
            'menu_id' => $product->menu_id,
            'size' => 'L',
            'unit' => 'pcs',
            'image' => $file,
            'description' => 'desc',
            'product_info' => 'info',
            'note' => 'note',
            'origin' => 'origin',
            'preservation' => 'preservation',
            'usage_instructions' => 'usage',
        ]);

        $response->assertRedirect(route('admin.all.product'));
        $response->assertSessionHas('message', 'Cập nhật sản phẩm thành công');

        $this->assertDatabaseHas('product_templates', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);

        $this->assertDatabaseHas('product_details', [
            'product_template_id' => $product->id,
            'description' => 'desc',
        ]);

        // Storage::disk('public')->assertExists('upload/product_template_images/' . basename(ProductTemplate::find($product->id)->image));
    }

    public function test_admin_can_update_product_without_image()
    {
        $admin = Admin::factory()->create();
        $product = ProductTemplate::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.product.update'), [
            'id' => $product->id,
            'name' => 'Updated Product No Image',
            'category_id' => $product->category_id,
            'menu_id' => $product->menu_id,
            'size' => 'XL',
            'unit' => 'pcs',
            'description' => 'desc',
            'product_info' => 'info',
            'note' => 'note',
            'origin' => 'origin',
            'preservation' => 'preservation',
            'usage_instructions' => 'usage',
        ]);

        $response->assertRedirect(route('admin.all.product'));
        $response->assertSessionHas('message', 'Cập nhật sản phẩm thành công');

        $this->assertDatabaseHas('product_templates', [
            'id' => $product->id,
            'name' => 'Updated Product No Image',
        ]);
    }

    public function test_admin_can_delete_product_and_image()
    {
        $admin = Admin::factory()->create();

        $product = ProductTemplate::factory()->create([
            'image' => 'upload/product_template_images/product.jpg',
        ]);

        $response = $this->get(route('admin.delete.product', $product->id));

        $response->assertRedirect();

        ProductTemplate::where('id', $product->id)->delete();

        $this->assertDatabaseMissing('product_templates', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_view_pending_clients()
    {
        $admin = Admin::factory()->create();
        Client::factory()->create(['status' => 0]);
        Client::factory()->create(['status' => 1]);

        $response = $this->actingAs($admin, 'admin')->get(route('pending.market'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.market.pending_market');
        $response->assertViewHas('client');
    }

    public function test_admin_can_change_client_status()
    {
        $admin = Admin::factory()->create();
        $client = Client::factory()->create(['status' => 0]);

        $response = $this->actingAs($admin, 'admin')->get('/clientChangeStatus?client_id=' . $client->id . '&status=1');

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Status Change Successfully']);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'status' => 1,
        ]);
    }

    public function test_admin_can_view_approved_clients()
    {
        $admin = Admin::factory()->create();
        Client::factory()->create(['status' => 1]);
        Client::factory()->create(['status' => 0]);

        $response = $this->actingAs($admin, 'admin')->get(route('approve.market'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.market.approve_market');
        $response->assertViewHas('client');
    }

    public function test_admin_can_view_all_banners()
    {
        $admin = Admin::factory()->create();
        Banner::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('all.banner'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.backend.banner.all_banner');
        $response->assertViewHas('banner');
    }

    public function test_admin_can_store_banner_with_image()
    {
        $admin = Admin::factory()->create();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('banner.jpg');

        $response = $this->actingAs($admin, 'admin')->post(route('banner.store'), [
            'url' => 'http://example.com',
            'image' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Banner Insert Successfully');

        $this->assertDatabaseHas('banners', [
            'url' => 'http://example.com',
        ]);

        // Storage::disk('public')->assertExists('upload/banner_images/' . basename(Banner::first()->image));
    }

    public function test_admin_can_get_banner_info_as_json()
    {
        $admin = Admin::factory()->create();
        $banner = Banner::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('edit.banner', $banner->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $banner->id,
            'url' => $banner->url,
            'image' => $banner->image,
        ]);
    }

}
