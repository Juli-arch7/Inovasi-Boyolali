<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InisiatorController;
use App\Http\Controllers\PublicController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public Routes
Route::get('/public/products', [PublicController::class, 'getCuratedProducts']);
Route::get('/public/products/{id}', [PublicController::class, 'getProductDetail']);
Route::get('/public/metadata', [PublicController::class, 'getMetadata']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Super Admin Routes
    Route::get('/superadmin/admins', [SuperAdminController::class, 'getAdmins']);
    Route::post('/superadmin/admins', [SuperAdminController::class, 'createAdmin']);
    Route::delete('/superadmin/admins/{id}', [SuperAdminController::class, 'deleteAdmin']);

    // Admin Routes
    Route::get('/admin/stats', [AdminController::class, 'getStats']);
    Route::get('/admin/products', [AdminController::class, 'getSubmissions']);
    Route::get('/admin/products/{id}', [AdminController::class, 'getProductDetail']);
    Route::put('/admin/products/{id}/verify', [AdminController::class, 'verifyProduct']);
    Route::put('/admin/products/{id}/toggle-active', [AdminController::class, 'toggleActive']);
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // Inisiator Routes
    Route::get('/inisiator/metadata', [InisiatorController::class, 'getMetadata']);
    Route::get('/inisiator/products', [InisiatorController::class, 'getMyProducts']);
    Route::post('/inisiator/products', [InisiatorController::class, 'submitProduct']);
    Route::get('/inisiator/products/{id}', [InisiatorController::class, 'getProductDetail']);
    Route::put('/inisiator/products/{id}', [InisiatorController::class, 'updateProduct']);
});
