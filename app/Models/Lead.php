<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'property_id',
        'nome',
        'telefone',
        'email',
        'mensagem',
        'origem',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
