<?php

namespace App\Services;

use App\Repositories\PermissionRepositoryInterface;

class MenuService
{
  protected $permissionRepository;
  protected $menus = [];

  public function __construct(
    PermissionRepositoryInterface $permissionRepository
  ) {
    $this->permissionRepository = $permissionRepository;
  }

  /**
   * Data init untuk kebutuhan client
   * @return array
   */
  public function getData($user = null): array
  {
    $permissions = $this->permissionRepository->getUserPermissions($user);
    $this->createMenus(config('app.menus'), $permissions);
    return [
      'permissions' => $permissions,
      'menus' => $this->menus,
    ];
  }

  private function createMenus($menus, $permissions = [], $parent = null)
  {
    foreach ($menus as $key => $menu) {
      // Cek permission untuk menu ini
      $menuPermission = $this->getMenuPermission($menu['name']);
      $hasPermission = in_array($menuPermission, $permissions);

      if ($hasPermission) {
        if (!empty($menu['children'])) {
          $childMenus = [];
          foreach ($menu['children'] as $child) {
            $childPermission = $this->getChildPermission($child['name'], $menu['name']);
            if (in_array($childPermission, $permissions)) {
              $child['title'] = __($child['title']);
              $childMenus[] = $child;
            }
          }

          if (!empty($childMenus)) {
            $menu['title'] = __($menu['title']);
            $menu['children'] = $childMenus;
            $this->menus[] = $menu;
          }
        } else {
          $menu['title'] = __($menu['title']);
          $this->menus[] = $menu;
        }
      }
    }
  }

  /**
   * Mendapatkan permission untuk menu berdasarkan nama menu
   */
  private function getMenuPermission($menuName)
  {
    $permissionMap = [
      'dashboard' => 'dashboard',
      'employees' => 'employees',
      'rent_rooms' => 'rent_rooms',
      'complaints' => 'complaints',
      'posts' => 'posts',
      'reports' => 'reports',
      'settings' => 'settings',
    ];

    return $permissionMap[$menuName] ?? $menuName;
  }

  /**
   * Mendapatkan permission untuk submenu berdasarkan nama menu dan parent
   */
  private function getChildPermission($childName, $parentName)
  {
    $permissionMap = [
      'settings' => [
        'rooms' => 'settings_rooms',
        'office_tools' => 'settings_office_tools',
        'roles' => 'settings_roles',
        'users' => 'settings_users',
      ]
    ];

    return $permissionMap[$parentName][$childName] ?? $childName;
  }
}
