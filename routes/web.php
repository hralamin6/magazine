<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Web\HomeComponent::class)->name('web.home');
Route::get('/post/{post:slug}', \App\Livewire\Web\PostDetailsComponent::class)->name('web.post.details');
Route::get('/categories/{category:id?}', \App\Livewire\Web\CategoryWisePostComponent::class)->name('web.category.wise.post');
Route::get('/tags/{tag:id?}', \App\Livewire\Web\TagWisePostComponent::class)->name('web.tag.wise.post');
Route::get('/users/{user:id?}', \App\Livewire\Web\UserWisePostComponent::class)->name('web.user.wise.post');
Route::get('/search', \App\Livewire\Web\SearchWisePostComponent::class)->name('web.search');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/posts', \App\Livewire\Web\PostCrudComponent::class)->name('web.post.crud');


    Route::get('app', \App\Livewire\App\DashboardComponent::class)->name('app.dashboard');
    Route::get('app/roles', \App\Livewire\App\RoleComponent::class)->name('app.roles');
    Route::get('app/backups', \App\Livewire\App\BackupComponent::class)->name('app.backups');
    Route::get('app/users', \App\Livewire\App\UserComponent::class)->name('app.users');
    Route::get('app/profile', \App\Livewire\App\ProfileComponent::class)->name('app.profile');
    Route::get('app/user/{user}', \App\Livewire\App\UserDetailComponent::class)->name('app.user.detail');
    Route::get('app/setting', \App\Livewire\App\SettingComponent::class)->name('app.setting');
    Route::get('app/chat', \App\Livewire\App\ChatComponent::class)->name('app.chat');
    Route::get('app/pages', \App\Livewire\App\PageComponent::class)->name('app.pages');
    Route::get('app/categories', \App\Livewire\App\CategoryComponent::class)->name('app.categories');
    Route::get('app/posts', \App\Livewire\App\PostComponent::class)->name('app.posts');
    Route::get('app/notifications', \App\Livewire\App\NotificationComponent::class)->name('app.notifications');
    Route::get('app/translate', \App\Livewire\App\TranslateComponent::class)->name('app.translate');

});

require __DIR__ . '/auth.php';


Route::post('/subscribe', function (Request $request) {
    $user = \Illuminate\Support\Facades\Auth::user();
    $user->updatePushSubscription(
        $request->endpoint,
        $request->keys['p256dh'],
        $request->keys['auth']
    );
    return response()->json(['success' => true], 200);
});



// web.php
Route::post('/subscribe-guest', function (Request $request) {
    $request->validate([
        'endpoint' => 'required',
        'keys.auth' => 'required',
        'keys.p256dh' => 'required',
    ]);

    \Minishlink\WebPush\Subscription::create([
        'endpoint' => $request->endpoint,
        'keys' => [
            'p256dh' => $request->keys['p256dh'],
            'auth' => $request->keys['auth'],
        ],
    ]);
    \DB::table('guest_subscriptions')->updateOrInsert(
        ['endpoint' => $request->endpoint],
        [
            'endpoint' => $request->endpoint,
            'public_key' => $request->keys['p256dh'],
            'auth_token' => $request->keys['auth'],
//            'content_encoding' => 'aesgcm',
        ]
    );

    return response()->json(['success' => true]);
});


Route::group(['as' => 'laravelpwa.'], function () {
    Route::get('/manifest.json', 'App\Http\Controllers\LaravelPWAController@manifestJson')
        ->name('manifest');
    Route::get('/offline/', 'LaravelPWAController@offline');
});

Route::get('cmd/{slug}', function ($slug = null) {
    Artisan::call($slug); // Replace 'your:command' with the actual command.
    $output = Artisan::output();
    return "<pre>" . htmlspecialchars($output) . "</pre>";
});


Route::get('{slug}', \App\Livewire\Web\PageComponent::class)->name('web.page');
