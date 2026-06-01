<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'conteudo',
        'meta_title',
        'meta_description',
        'ativo',
    ];
}
