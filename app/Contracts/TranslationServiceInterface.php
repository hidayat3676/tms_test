<?php

namespace App\Contracts;

interface TranslationServiceInterface
{
    public function createTranslation(array $data);

    public function updateTranslation(int $id, array $data);

    public function searchTranslations(array $filters);

    public function deleteTranslation(int $id);

    public function findTranslation(int $id);

    public function findByKeyAndLocale(string $key, string $locale);

    public function getByLocale(string $locale);

    public function allTranslations();
}
