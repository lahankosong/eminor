<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\AkuController;
use App\Http\Controllers\KamuController;
use App\Http\Controllers\KitaController;
use App\Http\Controllers\DiaController;
use App\Http\Controllers\KamuNoteController;
use App\Http\Controllers\NotificationController;



// Halaman utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// Google Auth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/community/threads', [ThreadController::class, 'index'])->name('community.threads');
Route::get('/community/thread/create', [ThreadController::class, 'create'])->name('community.thread.create');
Route::post('/community/thread', [ThreadController::class, 'store'])->name('community.thread.store');
Route::get('/community/thread/{id}', [ThreadController::class, 'show'])->name('community.thread.show');
Route::post('/community/thread/{id}/reply', [ThreadController::class, 'reply'])->name('community.thread.reply');
Route::delete('/community/thread/{id}', [ThreadController::class, 'destroy'])->name('community.thread.destroy');
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/conversation/{id}', [ChatController::class, 'showConversation'])->name('chat.conversation');
Route::post('/chat/start/{userId}', [ChatController::class, 'startConversation'])->name('chat.start');
Route::post('/chat/conversation/{id}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/chat/group/{id}', [ChatController::class, 'showGroup'])->name('chat.group');
Route::post('/chat/group/create', [ChatController::class, 'createGroup'])->name('chat.group.create');
Route::post('/chat/group/{id}/send', [ChatController::class, 'sendGroupMessage'])->name('chat.group.send');
Route::get('/logout', [GoogleController::class, 'logout'])->name('logout');

Route::post('/kamu/note', [KamuNoteController::class, 'store'])->name('kamu.note.store');
Route::put('/kamu/note/{id}', [KamuNoteController::class, 'update'])->name('kamu.note.update');
Route::delete('/kamu/note/{id}', [KamuNoteController::class, 'destroy'])->name('kamu.note.destroy');

// Edit/hapus komentar
Route::delete('/aku/{postId}/comment/{id}', [AkuController::class, 'destroyComment'])->name('aku.comment.destroy');
Route::delete('/kita/{postId}/comment/{id}', [KitaController::class, 'destroyComment'])->name('kita.comment.destroy');

// Edit post
Route::put('/aku/{id}', [AkuController::class, 'update'])->name('aku.update');
Route::put('/kita/{id}', [KitaController::class, 'update'])->name('kita.update');
Route::put('/kamu/{id}', [KamuController::class, 'update'])->name('kamu.update');
Route::delete('/kamu/{id}', [KamuController::class, 'destroy'])->name('kamu.destroy');

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

// Halaman per lagu
Route::get('/lagu/{slug}', [SongController::class, 'show'])->name('song.show');
Route::post('/lagu/{slug}/comment', [SongController::class, 'comment'])
     ->name('song.comment')->middleware('auth');

// Routes yang butuh login
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    
});

// Admin routes
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/create', [AdminController::class, 'create'])->name('create');
    Route::post('/store', [AdminController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [AdminController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [AdminController::class, 'destroy'])->name('destroy');
    Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    Route::get('/ai-agent', [AiAgentController::class, 'index'])->name('ai-agent');
    Route::post('/ai-agent/generate/{id}', [AiAgentController::class, 'generate'])->name('ai-agent.generate');
    Route::post('/ai-agent/save-selected', [AiAgentController::class, 'saveSelected'])->name('ai-agent.save-selected');
    Route::get('/ai-agent/history/{songId}', [AiAgentController::class, 'getHistory'])->name('ai-agent.history');
});

Route::middleware(['auth'])->group(function () {
    // Fanbase routes
    Route::get('/aku', [AkuController::class, 'index'])->name('aku');
    Route::post('/aku', [AkuController::class, 'store'])->name('aku.store');
    Route::delete('/aku/{id}', [AkuController::class, 'destroy'])->name('aku.destroy');
    Route::post('/aku/{id}/like', [AkuController::class, 'like'])->name('aku.like');
    Route::post('/aku/{id}/comment', [AkuController::class, 'comment'])->name('aku.comment');

    Route::get('/kamu', [KamuController::class, 'index'])->name('kamu');

    Route::get('/kita', [KitaController::class, 'index'])->name('kita');
    Route::post('/kita', [KitaController::class, 'store'])->name('kita.store');
    Route::delete('/kita/{id}', [KitaController::class, 'destroy'])->name('kita.destroy');
    Route::post('/kita/{id}/like', [KitaController::class, 'like'])->name('kita.like');
    Route::post('/kita/{id}/comment', [KitaController::class, 'comment'])->name('kita.comment');

    Route::get('/dia', [DiaController::class, 'index'])->name('dia');
    Route::get('/dia/conversation/{id}', [DiaController::class, 'conversation'])->name('dia.conversation');
    Route::post('/dia/start/{userId}', [DiaController::class, 'start'])->name('dia.start');
    Route::post('/dia/conversation/{id}/send', [DiaController::class, 'send'])->name('dia.send');
    Route::get('/dia/group/{id}', [DiaController::class, 'group'])->name('dia.group');
    Route::post('/dia/group/create', [DiaController::class, 'createGroup'])->name('dia.group.create');
    Route::post('/dia/group/{id}/send', [DiaController::class, 'sendGroup'])->name('dia.group.send');
});