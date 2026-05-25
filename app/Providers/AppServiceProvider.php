<?php

namespace App\Providers;

use App\Repositories\Contracts\AssessmentRepositoryInterface;
use App\Repositories\Contracts\AssessmentResultRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AssessmentRepository;
use App\Repositories\Eloquent\AssessmentResultRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\ActivityLogService;
use App\Services\AssessmentBatchService;
use App\Services\AssessmentService;
use App\Services\ProfileService;
use App\Services\QuestionService;
use App\Services\Scorers\DiscScorerService;
use App\Services\Scorers\MbtiScorerService;
use App\Services\Scorers\AgilityScorerService;
use App\Services\Scorers\BusinessInsightScorerService;
use App\Services\Scorers\MsdtScorerService;
use App\Services\Scorers\PapiKostickScorerService;
use App\Services\Scorers\SpmScorerService;
use App\Services\ShareService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AssessmentRepositoryInterface::class, AssessmentRepository::class);
        $this->app->bind(AssessmentResultRepositoryInterface::class, AssessmentResultRepository::class);

        $this->app->singleton(ActivityLogService::class, fn () => new ActivityLogService);
        $this->app->singleton(QuestionService::class, fn () => new QuestionService);
        $this->app->singleton(AssessmentBatchService::class, fn () => new AssessmentBatchService);
        $this->app->singleton(ShareService::class, fn () => new ShareService);
        $this->app->singleton(MbtiScorerService::class, fn ($app) => new MbtiScorerService($app->make(QuestionService::class)));
        $this->app->singleton(DiscScorerService::class, fn ($app) => new DiscScorerService($app->make(QuestionService::class)));
        $this->app->singleton(PapiKostickScorerService::class, fn ($app) => new PapiKostickScorerService($app->make(QuestionService::class)));
        $this->app->singleton(MsdtScorerService::class, fn ($app) => new MsdtScorerService($app->make(QuestionService::class)));
        $this->app->singleton(SpmScorerService::class, fn ($app) => new SpmScorerService($app->make(QuestionService::class)));
        $this->app->singleton(BusinessInsightScorerService::class, fn ($app) => new BusinessInsightScorerService($app->make(QuestionService::class)));
        $this->app->singleton(AgilityScorerService::class, fn ($app) => new AgilityScorerService($app->make(QuestionService::class)));

        $this->app->singleton(ProfileService::class, fn ($app) => new ProfileService($app->make(ActivityLogService::class)));

        $this->app->singleton(AssessmentService::class, function ($app) {
            return new AssessmentService(
                $app->make(AssessmentRepositoryInterface::class),
                $app->make(AssessmentResultRepositoryInterface::class),
                $app->make(QuestionService::class),
                $app->make(ShareService::class),
                $app->make(ActivityLogService::class),
                $app->make(MbtiScorerService::class),
                $app->make(DiscScorerService::class),
                $app->make(PapiKostickScorerService::class),
                $app->make(MsdtScorerService::class),
                $app->make(SpmScorerService::class),
                $app->make(BusinessInsightScorerService::class),
                $app->make(AgilityScorerService::class),
                $app->make(AssessmentBatchService::class),
            );
        });
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (! $user->hasRole('superadmin')) {
                return null;
            }

            // Master data hanya untuk HR/Admin per tenant, bukan superadmin.
            if (str_starts_with($ability, 'master_data.')) {
                return false;
            }

            return true;
        });

        $this->app->booted(function () {
            if (session()->has('locale')) {
                app()->setLocale(session('locale'));
            }
        });
    }
}
