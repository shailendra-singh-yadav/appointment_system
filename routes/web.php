<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ChatController;
use App\Events\MessageSent;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('posts', PostController::class);
    Route::post('/posts/reorder', [PostController::class, 'reorder'])->name('posts.reorder');
    //Appointment
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments.join', [AppointmentController::class, 'join'])->name('appointments.join');
    Route::post('/appointments/{booking}/cancel', [AppointmentController::class, 'appointmentCancel'])->name('appointments.cancel');

   
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

});


require __DIR__.'/auth.php';
