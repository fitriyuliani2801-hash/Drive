<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Automated Task Scheduler (Cron Job Pipeline)
|--------------------------------------------------------------------------
| Otomatisasi pengumpulan komentar publik (Auto-Scraper), NLP Text
| Pre-processing, dan Rekalkulasi Pemodelan Topik LDA.
*/
Schedule::command('lda:auto-run')->everyMinute();
