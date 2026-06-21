<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_exclusive' => $this->boolean('is_exclusive'),
            'is_off_market' => $this->boolean('is_off_market'),
            'show_in_home_selecao_especial' => $this->boolean('show_in_home_selecao_especial'),
            'show_in_home_mais_procurados' => $this->boolean('show_in_home_mais_procurados'),
            'show_in_home_visto_recentemente' => $this->boolean('show_in_home_visto_recentemente'),
            'aceita_permuta' => $this->boolean('aceita_permuta'),
            'mobiliado' => $this->boolean('mobiliado'),
            'aceita_financiamento' => $this->boolean('aceita_financiamento'),
            'quartos' => $this->emptyToNull($this->input('quartos')),
            'suites' => $this->emptyToNull($this->input('suites')),
            'banheiros' => $this->emptyToNull($this->input('banheiros')),
            'lavabos' => $this->emptyToNull($this->input('lavabos')),
            'garagens' => $this->emptyToNull($this->input('garagens')),
            'andar' => $this->emptyToNull($this->input('andar')),
            'area_total' => $this->emptyToNull($this->input('area_total')),
            'area_construida' => $this->emptyToNull($this->input('area_construida')),
            'valor_condominio' => $this->emptyToNull($this->input('valor_condominio')),
            'valor_iptu' => $this->emptyToNull($this->input('valor_iptu')),
            'ano_construcao' => $this->emptyToNull($this->input('ano_construcao')),
            'posicao_solar' => $this->emptyToNull($this->input('posicao_solar')),
            'business_type_ids' => array_values(array_filter((array) $this->input('business_type_ids', []))),
            'gallery_upload_tokens' => array_values(array_filter((array) $this->input('gallery_upload_tokens', []))),
            'special_category_ids' => array_values(array_filter((array) $this->input('special_category_ids', []))),
        ]);
    }

    protected function baseRules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'codigo_referencia' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'descricao' => ['required', 'string'],
            'tipo_propriedade_id' => ['required', 'integer', 'exists:property_types,id'],
            'condominium_id' => ['nullable', 'integer', 'exists:condominiums,id'],
            'business_type_ids' => ['required', 'array', 'min:1'],
            'business_type_ids.*' => ['integer', 'exists:business_types,id'],
            'valor_venda' => ['nullable', 'string'],
            'valor_locacao' => ['nullable', 'string'],
            'valor_condominio' => ['nullable', 'string'],
            'valor_iptu' => ['nullable', 'string'],
            'endereco' => ['required', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],
            'quartos' => ['nullable', 'integer', 'min:0'],
            'suites' => ['nullable', 'integer', 'min:0'],
            'banheiros' => ['nullable', 'integer', 'min:0'],
            'lavabos' => ['nullable', 'integer', 'min:0'],
            'garagens' => ['nullable', 'integer', 'min:0'],
            'andar' => ['nullable', 'integer', 'min:0'],
            'area_total' => ['nullable', 'numeric', 'min:0'],
            'area_construida' => ['nullable', 'numeric', 'min:0'],
            'aceita_permuta' => ['nullable', 'boolean'],
            'mobiliado' => ['nullable', 'boolean'],
            'aceita_financiamento' => ['nullable', 'boolean'],
            'ano_construcao' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'posicao_solar' => ['nullable', 'string', Rule::in([
                'Norte',
                'Sul',
                'Leste',
                'Oeste',
                'Nordeste',
                'Noroeste',
                'Sudeste',
                'Sudoeste',
            ])],
            'is_exclusive' => ['nullable', 'boolean'],
            'is_off_market' => ['nullable', 'boolean'],
            'show_in_home_selecao_especial' => ['nullable', 'boolean'],
            'show_in_home_mais_procurados' => ['nullable', 'boolean'],
            'show_in_home_visto_recentemente' => ['nullable', 'boolean'],
            'featured_upload_token' => ['nullable', 'uuid'],
            'gallery_upload_tokens' => ['nullable', 'array'],
            'gallery_upload_tokens.*' => ['uuid'],
            'special_category_ids' => ['nullable', 'array'],
            'special_category_ids.*' => ['integer', 'exists:special_categories,id'],
        ];
    }

    private function emptyToNull(mixed $value): mixed
    {
        return $value === '' ? null : $value;
    }
}
