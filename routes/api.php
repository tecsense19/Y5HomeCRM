<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/icons', function() {
    return response()->json(\App\Models\Icon::where('is_active', true)->orderBy('created_at', 'desc')->get());
});

Route::get('/locations', function() {
    return response()->json(\App\Models\Location::where('is_active', true)->orderBy('name')->get());
});

Route::get('/frame-colors', function() {
    return response()->json(\App\Models\FrameColor::where('is_active', true)->orderBy('series')->orderBy('name')->get());
});
