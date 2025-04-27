<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 直接回傳一個 JSON 格式的資料，用 "data" 包住
        return response()->json([
            'data' => Image::all()->map(function ($image) {
                $imageArray = $image->toArray();
                $imageArray['likedBy'] = $image->likedBy()->pluck('users.id')->toArray();
                $imageArray['imagePath'] = url(Storage::url($image->imagePath)); // 獲取圖片的完整 URL
                return $imageArray;
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageRequest $request)
    {
        // 驗證請求
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'vibe' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 限制圖片格式和大小
        ]);

        // 測試期，使用者ID為1
        $userId = 1;
        // 檢查使用者是否存在
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => '使用者不存在'
            ], 404);
        }

        // 處理圖片上傳
        if ($request->hasFile('image')) {
            // 獲取檔案
            $file = $request->file('image');
            // 生成唯一檔名
            $filename = time() . '_' . $file->getClientOriginalName();
            // 儲存檔案到 public/images 目錄
            $path = $file->storeAs('images', $filename, 'public');

            // 創建圖片記錄, 並指定為使用者的圖片
            $image = new Image([
                'name' => $request->input('name'),
                'vibe' => $request->input('vibe'),
                'imagePath' => $path,
            ]);

            // 先儲存圖片記錄到資料庫，這樣它才會有 id
            $image->save();

            // 將圖片與使用者關聯
            $image->users()->attach($user->id);
            $image->likedBy()->attach($user->id); // 預設喜歡自己的圖片

            return response()->json([
                'message' => '圖片上傳成功',
                'data' => $image
            ], 201);
        }

        return response()->json([
            'error' => true,
            'message' => '上傳失敗，請選擇圖片'
        ], 400);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return response()->json([
            'data' => $image->load('likedBy')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        $validated = $request->validated();

        // 更新圖片資訊
        $image->update([
            'name' => $validated['name'] ?? $image->name,
            'vibe' => $validated['vibe'] ?? $image->vibe,
        ]);

        // 如果有新圖片上傳，則替換舊圖片
        if ($request->hasFile('image')) {
            // 刪除舊圖片
            $image->deleteFile();
            // 上傳新圖片
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('images', $filename, 'public');

            $image->imagePath = $path;
            $image->save();
        }

        return response()->json([
            'message' => '圖片更新成功',
            'data' => $image
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        // 刪除圖片檔案
        $image->deleteFile();

        // 刪除資料庫記錄
        $image->delete();

        return response()->json([
            'message' => '圖片刪除成功'
        ]);
    }

    /**
     * Toggle like status for the image.
     */
    public function toggleLike(Image $image)
    {
        // $user = Auth::user();
        $user = User::find(1); // 假設用戶ID為1，實際應根據當前登錄用戶獲取
        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => '未登入'
            ], 401);
        }
        $test = $image->id;
        // 檢查用戶是否已經喜歡這張圖片
        if ($image->likedBy()->where('user_id', $user->id)->exists()) {
            // 如果已經喜歡，則取消喜歡
            $image->likedBy()->detach($user->id);
            $message = '已取消喜歡';
        } else {
            // 如果尚未喜歡，則添加喜歡
            $image->likedBy()->attach($user->id);
            $message = '已添加喜歡';
        }

        // return response()->json([
        //     'message' => $message,
        //     'likes_count' => $image->likedBy()->count()
        // ]);

        // 回傳 JSON 格式的資料
        $imageArray = $image->toArray();
        $imageArray['likedBy'] = $image->likedBy()->pluck('users.id')->toArray();

        return response()->json([
            'data' => $imageArray
        ]);
    }

    /**
     * Get all users who liked the image.
     */
    public function getLikes(Image $image)
    {
        return response()->json([
            'data' => $image->likedBy
        ]);
    }
}
