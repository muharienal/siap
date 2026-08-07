<?php

namespace App\Helpers;

use Carbon\Carbon;

class Formatter
{
   /**
   * Untuk formate dari date ke date db
   *
   * @param date $date
   * @return string
   */
  public static function toDbFormat($date)
  {
    return Carbon::parse($date)->format('Y-m-d');
  }

  /**
   * Untuk formate dari date ke client format
   *
   * @param date $date
   * @return string
   */
  public static function toClientFormat($date)
  {
    return Carbon::parse($date)->format('d/m/Y');
  }

  /**
   * Parse array object with index to array.
   * @param array $data
   * @return array
   */
  public static function arrayObjectToArray($dataObject = []): Array
  {
    $data = [];
    foreach ($dataObject as $key => $type) {
      $type['name'] = __($type['name']);
      $data[] = $type;
    }
    return $data;
  }
}
