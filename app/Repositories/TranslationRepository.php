<?php

namespace App\Repositories;

use App\Contracts\TranslationRepositoryInterface;
use App\Factories\ModelFactory;

class TranslationRepository implements TranslationRepositoryInterface
{

    private $model;

    public function __construct($model)
    {
        $this->model = ModelFactory::create($model);
    }

    public function create(array $data)
    {
       return $this->model::create($data);
    }

    function update(int $id, array $data)
    {
       return $this->model::where('id', $id)->update($data);
    }

    function search(array $filters)
    {
        return $this->model::query()
            ->select('id', 'key', 'locale', 'value', 'tag')
            ->when(isset($filters['key']), fn($q) => $q->where('key', '=', $filters['key']))
            ->when(isset($filters['value']), fn($q) => $q->where('value', 'like', "%{$filters['value']}%"))
            ->when(isset($filters['tag']), fn($q) => $q->where('tag', $filters['tag']))
            ->when(isset($filters['locale']), fn($q) => $q->where('locale', $filters['locale']))
            ->toBase()
            ->paginate(20);
    }

    function getByLocale(string $locale)
    {
        return $this->model::where('locale', $locale)
            ->select('id', 'key', 'locale', 'value', 'tag')
            ->toBase()
            ->paginate(20);
    }

    function delete(int $id)
    {
       return $this->model::where('id', $id)->delete();
    }

    function find(int $id)
    {
        return $this->model::toBase()->where('id', $id)->first();
    }

    public function all()
    {
        return $this->model::select('id', 'key', 'locale', 'value', 'tag')->toBase()->paginate(20);
    }

    public function findByKeyAndLocale(string $key, string $locale)
    {
       return $this->model::where('key', $key)->where('locale', $locale)->toBase()->first();
    }
}
