<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'codigo_anuncio',
        'codigo_referencia',
        'titulo',
        'slug',
        'descricao',
        'tipo_propriedade_id',
        'operacao',
        'valor',
        'moeda',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'id_localidade_xml',
        'localidade_xml',
        'latitud',
        'longitud',
        'area_util',
        'area_total',
        'quartos',
        'suites',
        'banheiros',
        'garagens',
        'condominio',
        'iptu',
        'destaque',
        'ativo',
        'data_modificacao_xml',
    ];

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'tipo_propriedade_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(PropertyFeature::class, 'property_property_feature', 'property_id', 'feature_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
