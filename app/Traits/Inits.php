<?php

namespace App\Traits;

use App\Helpers\Formatter;

trait Inits
{
  /**
   * List approval types.
   * @return array
   */
  public function approvalTypes(): array
  {
    return Formatter::arrayObjectToArray(config('app.approval_types'));
  }

  /**
   * List approval status types.
   * @return array
   */
  public function approvalStatuses(): array
  {
    return Formatter::arrayObjectToArray(config('app.approval_statuses'));
  }

  /**
   * Find Approval Status.
   * @param string
   * @return array
   */
  public function findApprovalStatus($id): array
  {
    $data = $this->approvalStatuses();
    return collect($data)->where('id', $id)->first();
  }
}
