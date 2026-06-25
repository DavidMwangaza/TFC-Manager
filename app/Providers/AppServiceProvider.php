<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Chapter;
use App\Models\Subject;
use App\Models\ThesisFile;
use App\Policies\ChapterPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\ThesisFilePolicy;
use Carbon\Carbon;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogFailedLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Chapter::class, ChapterPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(ThesisFile::class, ThesisFilePolicy::class);

        // Register Auth Listeners for Audit Logs
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Failed::class, LogFailedLogin::class);

        // Set Carbon locale
        Carbon::setLocale(config('app.locale'));
    }
}
