<?php

use Illuminate\Support\Facades\App;
use App\Models\Client;
use Cloudinary\Api\Upload\UploadApi;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Cloudinary SDK (giả định đã được cấu hình trong AppServiceProvider)
$uploadApi = new UploadApi();

$clients = Client::all();
echo "== Tổng số client: " . $clients->count() . " ==\n";

foreach ($clients as $client) {
    echo "- Client ID: {$client->id}, Tên: {$client->name}, Cover: {$client->cover_photo}\n";

    $filename = trim($client->cover_photo);

    if (!$filename) {
        $filename = 'upload/no_image.jpg';
    } elseif (!str_contains($filename, 'upload/')) {
        $filename = 'upload/client_images/' . $filename;
    }

    $localPath = public_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filename));

    if (file_exists($localPath)) {
        echo "  ✅ Tìm thấy file: $localPath\n";

        try {
            $response = $uploadApi->upload($localPath, ['folder' => 'client_images']);
            $imageUrl = $response['secure_url'];

            $client->cover_photo = $imageUrl;
            $client->save();

            echo "  ✅ Uploaded client ID {$client->id} thành công: $imageUrl\n";
        } catch (Exception $e) {
            echo "  ❌ Lỗi upload client ID {$client->id}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  ⚠️ Không tìm thấy ảnh hoặc đường dẫn sai: $localPath\n";
    }
}
