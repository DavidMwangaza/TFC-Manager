<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Planifier la vérification des SLA (relances professeurs et étudiants) toutes les heures
Schedule::command('check:milestone-sla')->hourly();
