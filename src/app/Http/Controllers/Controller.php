<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected static function array_key_replace($item, $replace_with, array $array): array
    {
        $updated_array = [];
        foreach ($array as $key => $value) {
            if (!is_array($value) && $key == $item) {
                $updated_array = array_merge($updated_array, [$replace_with => $value]);
                continue;
            }
            $updated_array = array_merge($updated_array, [$key => $value]);
        }
        return $updated_array;
    }
}
