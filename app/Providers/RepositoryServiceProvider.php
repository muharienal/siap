<?php

namespace App\Providers;

use App\Repositories\ApprovalLogRepositoryInterface;
use App\Repositories\ApprovalRepository;
use App\Repositories\ApprovalRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\EmployeeRepository;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\JobLevelRepository;
use App\Repositories\JobLevelRepositoryInterface;
use App\Repositories\JobPositionRepository;
use App\Repositories\JobPositionRepositoryInterface;
use App\Repositories\NumberRepository;
use App\Repositories\NumberRepositoryInterface;
use App\Repositories\OfficeToolRepository;
use App\Repositories\OfficeToolRepositoryInterface;
use App\Repositories\OrgStructureRepository;
use App\Repositories\OrgStructureRepositoryInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\PermissionRepositoryInterface;
use App\Repositories\PostRepository;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\RentRoomRepository;
use App\Repositories\RentRoomRepositoryInterface;
use App\Repositories\RoomRepository;
use App\Repositories\RoomRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
    $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
    $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
    $this->app->bind(RentRoomRepositoryInterface::class, RentRoomRepository::class);
    $this->app->bind(OfficeToolRepositoryInterface::class, OfficeToolRepository::class);
    $this->app->bind(OrgStructureRepositoryInterface::class, OrgStructureRepository::class);
    $this->app->bind(JobLevelRepositoryInterface::class, JobLevelRepository::class);
    $this->app->bind(JobPositionRepositoryInterface::class, JobPositionRepository::class);
    $this->app->bind(ApprovalRepositoryInterface::class, ApprovalRepository::class);
    $this->app->bind(ApprovalLogRepositoryInterface::class, ApprovalRepository::class);
    $this->app->bind(NumberRepositoryInterface::class, NumberRepository::class);
    $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}