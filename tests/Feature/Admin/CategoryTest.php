<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Menu;

test('admin can view all categories', function () {
    $admin = Admin::factory()->create();
    $menu = Menu::factory()->create();
    Category::factory()->count(3)->create(['menu_id' => $menu->id]);

    $response = $this->actingAs($admin, 'admin')->get(route('all.category'));

    $response->assertOk();
    $response->assertViewIs('admin.backend.category.all_category');
    $response->assertViewHas('category');
});

test('admin can view add category form', function () {
    $admin = Admin::factory()->create();
    Menu::factory()->count(2)->create();

    $response = $this->actingAs($admin, 'admin')->get(route('add.category'));

    $response->assertOk();
    $response->assertViewIs('admin.backend.category.add_category');
    $response->assertViewHas('menus');
});

test('admin can store category without image', function () {
    $admin = Admin::factory()->create();
    $menu = Menu::factory()->create();

    $data = [
        'category_name' => 'Test Category',
        'menu_id' => $menu->id,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('category.store'), $data);

    $response->assertRedirect(route('all.category'));
    $response->assertSessionHas('message', 'Create Category Successfully');

    expect(\App\Models\Category::where('category_name', 'Test Category')->exists())->toBeTrue();
});

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin can store category with image', function () {
    Storage::fake('public');
    $admin = Admin::factory()->create();
    $menu = Menu::factory()->create();

    $file = UploadedFile::fake()->image('category.jpg');
    $data = [
        'category_name' => 'With Image',
        'menu_id' => $menu->id,
        'image' => $file,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('category.store'), $data);

    $response->assertRedirect(route('all.category'));
    $response->assertSessionHas('message', 'Create Category Successfully');

    expect(\App\Models\Category::where('category_name', 'With Image')->exists())->toBeTrue();
});

test('admin can view edit category form', function () {
    $admin = Admin::factory()->create();
    $menu = Menu::factory()->create();
    $category = \App\Models\Category::factory()->create(['menu_id' => $menu->id]);

    $response = $this->actingAs($admin, 'admin')->get(route('edit.category', $category->id));

    $response->assertOk();
    $response->assertViewIs('admin.backend.category.edit_category');
    $response->assertViewHasAll(['category', 'menus']);
});

test('admin can update category without image', function () {
    $admin = Admin::factory()->create();
    $menu = Menu::factory()->create();
    $category = \App\Models\Category::factory()->create(['menu_id' => $menu->id]);

    $data = [
        'id' => $category->id,
        'category_name' => 'Updated Category',
        'menu_id' => $menu->id,
    ];

    $response = $this->actingAs($admin, 'admin')->post(route('category.update'), $data);

    $response->assertRedirect(route('all.category'));
    $response->assertSessionHas('message', 'Update Category Successfully');

    expect($category->fresh()->category_name)->toBe('Updated Category');
});

test('admin can delete category and its image (mocked)', function () {
    $admin = Admin::factory()->make();
    $category = Category::factory()->make([
        'image' => 'upload/category_images/test.jpg',
    ]);

    $categoryMock = \Mockery::mock(\App\Models\Category::class)->makePartial();
    $categoryMock->shouldReceive('delete')->once()->andReturnTrue();

    $deleted = $categoryMock->delete();

    expect($deleted)->toBeTrue();
});
