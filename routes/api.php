<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/tokens/create', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'token_name' => 'required',
    ]);

    // 2. Tìm user theo email
    $user = User::where('email', $request->email)->first();

    // 3. Kiểm tra user tồn tại và mật khẩu khớp
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Thông tin đăng nhập không chính xác.'],
        ]);
    }
    // 4. Bây giờ mới tạo Token từ đối tượng $user đã tìm thấy
    $token = $user->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});


Route::middleware('auth:sanctum')->get('/my-secret-rooms', [AuthController::class, 'index']);
