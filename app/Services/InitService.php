<?php

namespace App\Services;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\OfficeToolCollection;
use App\Http\Resources\RoomCollection;
use App\Http\Resources\UserResource;
use App\Repositories\EmployeeRepositoryInterface;
use App\Repositories\OfficeToolRepositoryInterface;
use App\Repositories\RoomRepositoryInterface;
use App\Traits\Inits;
use Illuminate\Support\Facades\Auth;

class InitService
{
  use Inits;
  protected $menuService;
  protected $employeeRepo;
  protected $roomRepo;
  protected $officeToolRepo;

  public function __construct(
    MenuService $menuService,
    EmployeeRepositoryInterface $employeeRepo,
    RoomRepositoryInterface $roomRepo,
    OfficeToolRepositoryInterface $officeToolRepo
  ) {

    $this->menuService = $menuService;
    $this->employeeRepo = $employeeRepo;
    $this->officeToolRepo = $officeToolRepo;
    $this->roomRepo = $roomRepo;
  }

  /**
   * Data init untuk kebutuhan client
   * @return array
   */
  public function getData($user = null): array
  {
    // UserResource::make($user)->resolve()
    $user = !is_null($user) ? $user : Auth::user();
    $dataUser = array_merge(
      [
        "name" => $user->name,
        "email" => $user->email,
        "card_number" => $user->card_number,
        "id" => $user->id,
        "is_superuser" => $user->hasRole('Administrator', 'api'),
      
      ],
      $this->menuService->getData($user)
    );
    $officeTools = $this->officeToolRepo->getAllQuery()->get();
    $rooms = $this->roomRepo->getAllQuery()->get();

    $data = [
      'config' => [
        'approval_statuses' => $this->approvalStatuses(),
        'approval_types' => $this->approvalTypes()
      ],
      'master' => [
        'office_tools' => new OfficeToolCollection($officeTools),
        'rooms' => new RoomCollection($rooms),
      ],
      'user' => $dataUser,
      'options' => [
        'download_apk' => $this->downloadApk(),
      ]
    ];
    $employee = $this->employeeRepo->findBy('user_id', $user->id);
    if (!empty($employee)) {
      $data['employee'] = new EmployeeResource($employee);
    }

    return $data;
  }

  private function downloadApk()
  {
    return 'https://www.upload-apk.com/vkbpsL1p1a6Q1m9';
  }
}
