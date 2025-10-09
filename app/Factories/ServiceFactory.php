<?php

namespace App\Factories;

use App\Repositories\TranslationRepository;
use App\Services\TranslationService;

class ServiceFactory
{
    public static function create($type)
    {
        switch($type)
        {
            case 'translation':
                return new TranslationService((new TranslationRepository('eloquent')));
            default:
                throw new \Exception("Unknown type");
        }
    }
}