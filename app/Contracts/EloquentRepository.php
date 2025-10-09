<?php

namespace App\Contracts;

interface EloquentRepository
{
    function create(array $data): self;
    function update(array $data): self;
    function query(): self;
    function where(...$arg): self;
    function select(...$arg): self;
    function get(...$arg);
}