<?php

namespace Database\Factories;

use App\Models\Image;
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
        ];
    }

    // 使用實際的檔案名稱
    public function fromExistingFile($filename, $path)
    {
        return $this->state(function (array $attributes) use ($filename, $path) {
            return [
                'name' => $this->faker->name,
                'vibe' => $this->faker->paragraph,
                'imagePath' => $path,
            ];
        });
    }
}
