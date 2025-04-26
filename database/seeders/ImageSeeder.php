<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // 清除中間表記錄
        DB::table('image_user')->truncate();
        DB::table('image_likes')->truncate();

        // 獲取 images 目錄下的所有檔案
        $files = Storage::disk('public')->files('images');

        foreach ($files as $file) {
            $filename = basename($file);

            // 使用 factory 創建圖片記錄
            $image = Image::factory()->fromExistingFile($filename, $file)->create();

            // 為圖片隨機關聯 1-5 個用戶 (image_user 表)
            $userCount = rand(1, 5);
            $userIds = User::inRandomOrder()->limit($userCount)->pluck('id')->toArray();
            if (!empty($userIds)) {
                $image->users()->attach($userIds);
            }

            // 為圖片隨機關聯 1-5 個喜歡的用戶 (image_likes 表)
            $likeCount = rand(1, 5);
            $likedByUserIds = User::inRandomOrder()->limit($likeCount)->pluck('id')->toArray();
            if (!empty($likedByUserIds)) {
                $image->likedBy()->attach($likedByUserIds);
            }
        }
    }
}
