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
use App\Http\Controllers\PromoTemplateController;
use App\Http\Controllers\ContentCalendarController;
use App\Http\Controllers\MusicianController;
use App\Http\Controllers\BandPostController;
use App\Http\Controllers\GigPostController;
use App\Http\Controllers\AkuController;
use App\Http\Controllers\KamuController;
use App\Http\Controllers\KitaController;
use App\Http\Controllers\DiaController;
use App\Http\Controllers\KamuNoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ArticleController;



// Halaman utama — track kunjungan landing page
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('trackvisit:homepage');

// SEO: sitemap.xml dinamis
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

// Tools publik — tanpa login
Route::get('/tools', [ToolController::class, 'hub'])->name('tools.index');
Route::get('/tools/potong-lagu', [ToolController::class, 'audioCutter'])->name('tools.potong-lagu');
Route::get('/tools/hapus-vokal', [ToolController::class, 'vocalRemover'])->name('tools.hapus-vokal');
Route::get('/tools/cover-art', [ToolController::class, 'coverMaker'])->name('tools.cover-art');
Route::get('/tools/kartu-rilis', [ToolController::class, 'releaseCard'])->name('tools.kartu-rilis');
Route::get('/tools/countdown', [ToolController::class, 'countdown'])->name('tools.countdown');
Route::get('/tools/edit-metadata', [ToolController::class, 'editMetadata'])->name('tools.edit-metadata');
Route::get('/tools/chord-builder', [ToolController::class, 'chordBuilder'])->name('tools.chord-builder');
Route::get('/tools/bpm-kalkulator', [ToolController::class, 'bpmCalculator'])->name('tools.bpm-kalkulator');
Route::get('/tools/kalkulator-royalti', [ToolController::class, 'royaltyCalculator'])->name('tools.kalkulator-royalti');
Route::get('/tools/rate-card', [ToolController::class, 'rateCard'])->name('tools.rate-card');
Route::get('/tools/transpose-kunci', [ToolController::class, 'transposeKey'])->name('tools.transpose-kunci');
Route::get('/tools/epk', [ToolController::class, 'epkGenerator'])->name('tools.epk');
Route::get('/tools/setlist', [ToolController::class, 'setlistBuilder'])->name('tools.setlist');
Route::get('/tools/release-planner', [ToolController::class, 'releasePlanner'])->name('tools.release-planner');

// Gig Board publik — blur gate, apply wajib login
Route::get('/gig', [GigPostController::class, 'publicBoard'])->name('gig.board');

// Library (diskografi)
Route::get('/library', [LibraryController::class, 'index'])->name('library');

// Library Materi — publik baca, auth untuk unduh
Route::get('/library/materi', [ArticleController::class, 'index'])->name('library.materi');
Route::get('/library/materi/{slug}', [ArticleController::class, 'show'])->name('library.materi.show');
Route::get('/library/materi/{slug}/unduh', [ArticleController::class, 'download'])
     ->middleware('auth')->name('library.materi.download');

// Google Auth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/auth/google/onetap', [GoogleController::class, 'oneTap'])->name('google.onetap');
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


// Halaman per lagu
Route::get('/lagu/{slug}', [SongController::class, 'show'])->name('song.show');
// Catat pemutaran lagu (publik, fire-and-forget) — dibatasi throttle agar tak diabuse
Route::post('/lagu/{id}/play', [SongController::class, 'play'])
     ->whereNumber('id')->middleware('throttle:120,1')->name('song.play');

// Profil musisi PUBLIK (untuk link yang dibagikan) — guest lihat teaser, member lihat lengkap
Route::get('/musisi/{id}', [MusicianController::class, 'show'])->whereNumber('id')->name('musisi.show');
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
    Route::post('/blast-welcome', [AdminController::class, 'blastWelcome'])->name('blast-welcome');
    Route::get('/insights', [AdminController::class, 'insights'])->name('insights');
    Route::post('/insights/analyze', [AdminController::class, 'analyzeInsights'])->name('insights.analyze');
    Route::post('/insights/seo', [AdminController::class, 'seoSuggest'])->name('insights.seo');
    Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    Route::get('/promo', [PromoTemplateController::class, 'index'])->name('promo');
    Route::get('/calendar', [ContentCalendarController::class, 'index'])->name('calendar');
    Route::post('/calendar', [ContentCalendarController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/{id}', [ContentCalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{id}', [ContentCalendarController::class, 'destroy'])->name('calendar.destroy');
    Route::get('/ai-agent', [AiAgentController::class, 'index'])->name('ai-agent');
    Route::get('/ai-settings', [AiAgentController::class, 'aiSettings'])->name('ai-settings');
    Route::get('/audio-cut', [AiAgentController::class, 'audioCut'])->name('audio-cut');
    Route::get('/video-builder', [AiAgentController::class, 'videoBuilder'])->name('video-builder');
    Route::post('/ai-agent/generate/{id}', [AiAgentController::class, 'generate'])->name('ai-agent.generate');
    Route::post('/ai-agent/provider', [AiAgentController::class, 'storeProvider'])->name('ai-agent.provider.store');
    Route::put('/ai-agent/provider/{id}', [AiAgentController::class, 'updateProvider'])->name('ai-agent.provider.update');
    Route::delete('/ai-agent/provider/{id}', [AiAgentController::class, 'destroyProvider'])->name('ai-agent.provider.destroy');
    Route::post('/ai-agent/schedule', [AiAgentController::class, 'scheduleToCalendar'])->name('ai-agent.schedule');
    Route::post('/ai-agent/settings', [AiAgentController::class, 'saveSettings'])->name('ai-agent.settings');
    Route::post('/ai-agent/image', [AiAgentController::class, 'generateImage'])->name('ai-agent.image');
    Route::delete('/ai-agent/image/{id}', [AiAgentController::class, 'destroyImage'])->name('ai-agent.image.destroy');
    Route::post('/ai-agent/tts', [AiAgentController::class, 'generateTts'])->name('ai-agent.tts');
    Route::post('/ai-agent/transcribe', [AiAgentController::class, 'transcribe'])->name('ai-agent.transcribe');
});

Route::middleware(['auth'])->group(function () {
    // Fanbase routes — track entry pertama ke fanbase per sesi
    Route::get('/aku', [AkuController::class, 'index'])->name('aku')->middleware('trackvisit:fanbase');
    Route::post('/aku', [AkuController::class, 'store'])->name('aku.store');
    Route::delete('/aku/{id}', [AkuController::class, 'destroy'])->name('aku.destroy');
    Route::post('/aku/{id}/like', [AkuController::class, 'like'])->name('aku.like');
    Route::post('/aku/{id}/comment', [AkuController::class, 'comment'])->name('aku.comment');

    Route::get('/kamu', [KamuController::class, 'index'])->name('kamu')->middleware('trackvisit:fanbase');

    Route::get('/kita', [KitaController::class, 'index'])->name('kita')->middleware('trackvisit:fanbase');
    Route::post('/kita', [KitaController::class, 'store'])->name('kita.store');
    Route::delete('/kita/{id}', [KitaController::class, 'destroy'])->name('kita.destroy');
    Route::post('/kita/{id}/like', [KitaController::class, 'like'])->name('kita.like');
    Route::post('/kita/{id}/comment', [KitaController::class, 'comment'])->name('kita.comment');
    Route::post('/kita/{postId}/comment/{commentId}/like', [KitaController::class, 'likeComment'])->name('kita.comment.like');

    // Ecosystem Fase 1 — Direktori Musisi
    Route::get('/musisi', [MusicianController::class, 'index'])->name('musisi.index');
    Route::get('/musisi/profil', [MusicianController::class, 'edit'])->name('musisi.edit');
    Route::post('/musisi/profil', [MusicianController::class, 'save'])->name('musisi.save');
    Route::get('/musisi/card/{userId}', [MusicianController::class, 'card'])->whereNumber('userId')->name('musisi.card');
    Route::post('/follow/{userId}', [MusicianController::class, 'toggleFollow'])->whereNumber('userId')->name('follow.toggle');

    // Cari Personil (band posts)
    Route::get('/band', [BandPostController::class, 'index'])->name('band.index');
    Route::get('/band/create', [BandPostController::class, 'create'])->name('band.create');
    Route::post('/band', [BandPostController::class, 'store'])->name('band.store');
    Route::get('/band/{id}', [BandPostController::class, 'show'])->whereNumber('id')->name('band.show');
    Route::put('/band/{id}/status', [BandPostController::class, 'toggleStatus'])->whereNumber('id')->name('band.status');
    Route::delete('/band/{id}', [BandPostController::class, 'destroy'])->whereNumber('id')->name('band.destroy');

    // Papan Gig
    // Onboarding: simpan peran user
    Route::post('/onboarding/roles', [OnboardingController::class, 'saveRoles'])->name('onboarding.roles');

    Route::get('/gig/create', [GigPostController::class, 'create'])->name('gig.create');
    Route::post('/gig', [GigPostController::class, 'store'])->name('gig.store');
    Route::get('/gig/{id}/edit', [GigPostController::class, 'edit'])->whereNumber('id')->name('gig.edit');
    Route::put('/gig/{id}', [GigPostController::class, 'update'])->whereNumber('id')->name('gig.update');
    Route::put('/gig/{id}/status', [GigPostController::class, 'toggleStatus'])->whereNumber('id')->name('gig.status');
    Route::delete('/gig/{id}', [GigPostController::class, 'destroy'])->whereNumber('id')->name('gig.destroy');

    Route::get('/dia', [DiaController::class, 'index'])->name('dia')->middleware('trackvisit:fanbase');
    Route::get('/dia/conversation/{id}', [DiaController::class, 'conversation'])->name('dia.conversation');
    Route::post('/dia/start/{userId}', [DiaController::class, 'start'])->name('dia.start');
    Route::post('/dia/conversation/{id}/send', [DiaController::class, 'send'])->name('dia.send');
    Route::get('/dia/group/{id}', [DiaController::class, 'group'])->name('dia.group');
    Route::post('/dia/group/create', [DiaController::class, 'createGroup'])->name('dia.group.create');
    Route::post('/dia/group/{id}/send', [DiaController::class, 'sendGroup'])->name('dia.group.send');
    Route::get('/dia/conversation/{id}/poll', [DiaController::class, 'pollMessages'])->name('dia.poll');
    Route::get('/dia/group/{id}/poll', [DiaController::class, 'pollGroupMessages'])->name('dia.group.poll');
    Route::post('/dia/ping', [DiaController::class, 'ping'])->name('dia.ping');
    Route::post('/dia/locate', [DiaController::class, 'locate'])->name('dia.locate');
    Route::post('/dia/upload', [DiaController::class, 'uploadMedia'])->name('dia.upload');
    Route::post('/dia/invite/{id}/accept',  [DiaController::class, 'acceptInvite'])->name('dia.invite.accept');
    Route::post('/dia/invite/{id}/decline', [DiaController::class, 'declineInvite'])->name('dia.invite.decline');

    // Catatan kamu (private notes)
    Route::post('/kamu/note', [KamuNoteController::class, 'store'])->name('kamu.note.store');
    Route::put('/kamu/note/{id}', [KamuNoteController::class, 'update'])->name('kamu.note.update');
    Route::delete('/kamu/note/{id}', [KamuNoteController::class, 'destroy'])->name('kamu.note.destroy');
    Route::put('/kamu/{id}', [KamuController::class, 'update'])->name('kamu.update');
    Route::delete('/kamu/{id}', [KamuController::class, 'destroy'])->name('kamu.destroy');

    // Edit/hapus post
    Route::put('/aku/{id}', [AkuController::class, 'update'])->name('aku.update');
    Route::put('/kita/{id}', [KitaController::class, 'update'])->name('kita.update');

    // Hapus komentar
    Route::delete('/aku/{postId}/comment/{id}', [AkuController::class, 'destroyComment'])->name('aku.comment.destroy');
    Route::delete('/kita/{postId}/comment/{id}', [KitaController::class, 'destroyComment'])->name('kita.comment.destroy');

    // Reverse geocoding (proxy + cache Nominatim)
    Route::get('/geocode', [GeocodeController::class, 'reverse'])->middleware('throttle:60,1')->name('geocode');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');

    // Web Push (notifikasi sistem Android via service worker)
    Route::post('/push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::post('/push/test', [\App\Http\Controllers\PushController::class, 'test'])->name('push.test');
});