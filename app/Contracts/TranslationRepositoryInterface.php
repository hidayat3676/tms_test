<?php

namespace App\Contracts;

interface TranslationRepositoryInterface
{
    public function create(array $data);

    public function update(int $id, array $data);

    public function search(array $filters);

    public function find(int $id);

    public function findByKeyAndLocale(string $key, string $locale);

    public function getByLocale(string $locale);

    public function delete(int $id);

    public function all();
}
