<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\DeclarationFactory;

class Declaration extends Model
{
    use HasFactory; // @phpstan-ignore-line

    protected static function newFactory() : DeclarationFactory
    {
        return DeclarationFactory::new();
    }

    protected $fillable = [
        'title'
    ];
}
