<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word . '.jpg',
            'vibe' => $this->faker->paragraph,
            'imagePath' => 'images/' . $this->faker->word . '.jpg',
            'likedBy' => json_encode([]), // 默認為空陣列
        ];
    }

    public function fromExistingFile($filename, $path)
    {
        return $this->state(function (array $attributes) use ($filename, $path) {
        // 獲取 1-5 個隨機現有用戶 ID
        $count = rand(1, 5);
        $existingUserIds = User::inRandomOrder()->limit($count)->pluck('id')->toArray();

        // 如果沒有找到用戶，返回空陣列
        if (empty($existingUserIds)) {
            $existingUserIds = [];
        }

            return [
                'name' => $this->faker->name,
                'vibe' => $this->faker->paragraph,
                'imagePath' => $path,
            'likedBy' => json_encode($existingUserIds), // 將用戶 ID 陣列轉換為 JSON 字符串
            ];
        });
    }
}
