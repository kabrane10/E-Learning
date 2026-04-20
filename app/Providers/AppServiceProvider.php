<?php

namespace App\Providers;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Policies\ForumCategoryPolicy;
use App\Policies\ForumTopicPolicy;
use App\Policies\ForumPostPolicy;
use Livewire\Livewire;
use App\Livewire\NotificationCenter;
use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        ForumCategory::class => ForumCategoryPolicy::class,
        ForumTopic::class => ForumTopicPolicy::class,
        ForumPost::class => ForumPostPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Livewire::component('notification-center', NotificationCenter::class);
    }
}