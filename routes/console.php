<?php

use App\Jobs\FetchSocialPostsJob;
use App\Services\Social\BlueskyAdapter;
use App\Services\Social\DevToAdapter;
use App\Services\Social\XAdapter;
use App\Services\Social\YouTubeAdapter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Social Feed Scheduled Jobs
|--------------------------------------------------------------------------
|
| X and Bluesky are fetched every 15 minutes for near real-time content.
| YouTube and DEV.to are fetched hourly since their content moves slower.
|
*/

Schedule::job(new FetchSocialPostsJob(XAdapter::class))
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new FetchSocialPostsJob(BlueskyAdapter::class))
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new FetchSocialPostsJob(YouTubeAdapter::class))
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(new FetchSocialPostsJob(DevToAdapter::class))
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();
