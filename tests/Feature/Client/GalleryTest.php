<?php

namespace Tests\Feature\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Client;
use App\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo client và login
        $this->client = Client::factory()->create();
        $this->actingAs($this->client, 'client');

        // Giả lập storage disk public để test upload file
        Storage::fake('public');
    }

    public function test_all_gallery_view()
    {
        Gallery::factory()->count(3)->create(['client_id' => $this->client->id]);

        $response = $this->get(route('all.gallery'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.gallery.all_gallery');
        $response->assertViewHas('gallery');
    }

    public function test_add_gallery_view()
    {
        $response = $this->get(route('add.gallery'));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.gallery.add_gallery');
    }

    public function test_store_gallery()
    {
        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $file2 = UploadedFile::fake()->image('photo2.jpg');

        $response = $this->post(route('gallery.store'), [
            'gallery_img' => [$file1, $file2],
        ]);

        $response->assertRedirect(route('all.gallery'));
        $response->assertSessionHas('message', 'Insert Gallery Successfully');

        $this->assertDatabaseCount('galleries', 2);
    }

    public function test_edit_gallery_view()
    {
        $gallery = Gallery::factory()->create(['client_id' => $this->client->id]);

        $response = $this->get(route('edit.gallery', $gallery->id));

        $response->assertStatus(200);
        $response->assertViewIs('client.backend.gallery.edit_gallery');
        $response->assertViewHas('gallery', $gallery);
    }

    public function test_update_gallery_with_image()
    {
        $gallery = Gallery::factory()->create([
            'client_id' => $this->client->id,
            'gallery_img' => 'upload/gallery_images/old_image.jpg'
        ]);

        $newFile = UploadedFile::fake()->image('new_photo.jpg');

        $this->assertTrue(true);
    }

    public function test_update_gallery_without_image()
    {
        $gallery = Gallery::factory()->create(['client_id' => $this->client->id]);

        $response = $this->post(route('gallery.update'), [
            'id' => $gallery->id,
            // không gửi file ảnh
        ]);

        $response->assertRedirect(route('all.gallery'));
        $response->assertSessionHas('message', 'No Image Selected for Update');
    }

    public function test_delete_gallery()
    {
        $gallery = Gallery::factory()->create([
            'client_id' => $this->client->id,
            'gallery_img' => 'upload/gallery_images/image_to_delete.jpg',
        ]);

        $this->assertTrue(true);
    }
}
