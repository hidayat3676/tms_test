<?php

namespace App\Factories;

use App\Models\Translation;

class ModelFactory
{
    public static function create($type)
    {
        switch($type)
        {
            case 'eloquent':
                return new Translation();
            default:
                throw new \Exception("Unknown notification type");
        }
    }
}