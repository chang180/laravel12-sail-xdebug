<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;

    /**
     * 可批量賦值的屬性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'vibe',
        'imagePath',
    ];

    /**
     * 獲取與圖片關聯的用戶
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'image_user')
                    ->withTimestamps();
    }

    /**
     * 獲取喜歡這張圖片的用戶
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'image_likes', 'image_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * 獲取圖片的完整 URL
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->imagePath);
    }

    /**
     * 獲取圖片的完整路徑
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        return storage_path('app/public/' . $this->imagePath);
    }

    /**
     * 檢查圖片文件是否存在
     *
     * @return bool
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->imagePath);
    }

    /**
     * 刪除圖片文件
     *
     * @return bool
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return Storage::disk('public')->delete($this->imagePath);
}

        return false;
    }
}
