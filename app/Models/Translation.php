<?php

namespace App\Models;

use App\Contracts\EloquentRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'locale', 'value', 'tag'];
}
