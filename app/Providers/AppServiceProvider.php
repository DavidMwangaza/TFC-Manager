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
    }
}
