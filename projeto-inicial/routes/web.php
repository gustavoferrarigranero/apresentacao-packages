<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/demo/feedback');

Route::view('/demo/feedback', 'demo')->name('feedback.demo');

Route::get('/api/health', fn () => response()->json([
    'status' => 'ok',
    'stage' => 'starter',
]));
