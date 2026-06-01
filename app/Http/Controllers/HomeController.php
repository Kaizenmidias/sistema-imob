<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Home', [
            'properties' => [],
        ]);
    }

    public function properties(): Response
    {
        return Inertia::render('Properties');
    }

    public function sell(): Response
    {
        return Inertia::render('Sell');
    }

    public function showProperty(string $slug): Response
    {
        // Dados de exemplo para o imóvel
        $property = [
            'slug' => $slug,
            'title' => 'Casa à venda no Residencial Alphaville',
            'address' => 'Alphaville, Santana de Parnaíba/SP',
            'price' => 3600000,
            'type' => 'Venda',
            'propertyType' => 'Casa',
            'code' => 'CNC6099',
            'bedrooms' => 4,
            'bathrooms' => 4,
            'garages' => 4,
            'area' => 425,
            'lotArea' => 1000,
            'condominium' => 0,
            'iptu' => 661,
            'description' => <<<'HTML'
            <p>A experiência de uma casa estilo "fazenda" sem abrir mão da praticidade de estar em uma das localizações mais práticas de Alphaville. São 1.000 m² de área de terreno, 425 m² construídos imersos no verde de um paisagismo único.</p>
            <p>Esta é a casa que você ouvirá daqui a alguns anos seu filhos contarem as lembranças de quando eram mais novos... Os aniversários, churrascos com amigos, o parquinho que você montou no gramado.</p>
            <p>O lazer é aberto, gramado, com um pomar maravilhoso. Um pé de manga de + de 15 anos, jabuticaba, amora, nêspera, limão e tantos outros. Um acesso direto do gramado para um parquinho colado nos fundos da casa. Minutos a pé da área do lazer do condomínio, menos de 500 metros da portaria.</p>
            HTML,
            'photos' => [
                'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1200&h=800&fit=crop',
                'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=1200&h=800&fit=crop',
                'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1200&h=800&fit=crop',
                'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=1200&h=800&fit=crop',
                'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200&h=800&fit=crop',
            ],
        ];

        return Inertia::render('PropertyShow', [
            'property' => $property,
        ]);
    }
}
