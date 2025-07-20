<?php

namespace App\Services;

use App\Contracts\TranslationRepositoryInterface;
use App\Contracts\TranslationServiceInterface;

class TranslationService implements TranslationServiceInterface
{
    private $repository;

    public function __construct(TranslationRepositoryInterface $translationRepository)
    {
        $this->repository = $translationRepository;
    }

    public function createTranslation(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateTranslation(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function searchTranslations(array $filters)
    {
        return $this->repository->search($filters);
    }

    public function deleteTranslation(int $id)
    {
        return $this->repository->delete($id);
    }

    public function findTranslation(int $id)
    {
        return $this->repository->find($id);
    }

    public function allTranslations()
    {
        return $this->repository->all();
    }

    public function getByLocale(string $locale)
    {
        return $this->repository->getByLocale($locale);
    }

    public function findByKeyAndLocale(string $key, string $locale)
    {
        return $this->repository->findByKeyAndLocale($key, $locale);
    }
}
