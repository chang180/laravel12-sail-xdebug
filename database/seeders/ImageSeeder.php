<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清除現有圖片記錄
        Image::truncate();

        // 獲取 images 目錄下的所有檔案
        $files = Storage::disk('public')->files('images');

        foreach ($files as $file) {
            $filename = basename($file);

            // 使用 factory 創建資料庫記錄
            Image::factory()->fromExistingFile($filename, $file)->create();
        }
    }
}
