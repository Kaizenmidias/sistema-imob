<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condominiums', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $names = [
            '18 do Forte Residencial',
            'Acqua Park',
            'Alpha Club',
            'Alpha Conde',
            'Alpha Garden',
            'Alpha Gran',
            'Alpha Green',
            'Alpha House 1',
            'Alpha Life Copacabana',
            'Alpha Park',
            'Alpha Plus',
            'Alpha Square',
            'Alpha Style',
            'Alpha Vert',
            'Alpha Vita',
            'Alphama',
            'Alphasítio',
            'Alphaville 01',
            'Alphaville 02',
            'Alphaville 03',
            'Alphaville 04',
            'Alphaville 05',
            'Alphaville 06',
            'Alphaville 08',
            'Alphaville 09',
            'Alphaville 10',
            'Alphaville 11',
            'Alphaville 12',
            'Alphaville Zero',
            'América 1',
            'América 2',
            'Ápice Park',
            'Aruanã 601',
            'Atria',
            'Beat',
            'Bellagio',
            'Bellini',
            'Bonnard 307',
            'Boulevard Tamboré',
            'Brascan',
            'Burle Marx',
            'Californian Towers',
            'Campos do Conde',
            'Canvas High Houses',
            'Cauaxi Plaza',
            'Centro Comercial',
            'Centro de apoio 2',
            'Centro Empresarial Araguaia',
            'Chateau',
            'Choice',
            'Classic',
            'Columbia',
            'Concorde',
            'Condomínio Loft',
            'Copacabana',
            'De Ville (Av. Marte)',
            'De Ville (Mamoré)',
            'Duplex House',
            'Edifício Itapecuru',
            'Ekko Live',
            'Ereditá',
            'Essência',
            'Europa',
            'Everest Tower',
            'Fatto Alphaville',
            'Fiori',
            'Gama Offices',
            'Gênesis 1',
            'Gênesis 2',
            'Ghaia Tamboré',
            'Glass Alphaville',
            'Gran Alphaville',
            'Grand Floridian',
            'Green Tamboré',
            'Hit',
            'Iakatu',
            'Igloo',
            'Infinity',
            'Itahyê',
            'Jardins de Monet',
            'Jardins de Tamboré',
            'KAA Home',
            "L'Etoile",
            'Level',
            'Life Park',
            'Link Offices',
            'Link Studios',
            'Liv',
            'London Ville',
            'Lotus',
            'Lumina',
            'Madison',
            'Manhattan',
            'Master',
            'Medic Life',
            'Melville',
            'Metropolis',
            'Mont Blanc',
            'Monte Carlo',
            'More',
            'Murano',
            'Myrá',
            'Neo Alphaville',
            'Novare',
            'Oásis Alphaville',
            'Office Grajaú',
            'Oiapoque',
            'Oka',
            'On The Park',
            'One Gramercy',
            'Origem',
            'Pacific Towers',
            'Paisagem Tamboré',
            'Panoramic',
            'Paratii',
            'Parc Athenee',
            'Parque Tamboré',
            'Personal Business Office',
            'Phanton',
            'Plaza',
            'Polo Industrial Tamboré',
            'Premium Tamboré',
            'Present',
            'Quality Hotel',
            'Quebec',
            'Quintas Tamboré',
            'Regina',
            'Reserva Alphasítio',
            'Resort Tamboré',
            'Royal Park',
            'Saint Paul',
            'Saint Thomas',
            'San Francisco',
            'Santiago',
            'Scenic',
            'Sequóia',
            'Shopping Service',
            'Singular',
            'Smart Office',
            'Sol Alphaville',
            'Soul Itapecuru',
            'Splendore',
            'Splendya 1',
            'Stadium Corporate',
            'Sunset Itapecuru',
            'Tamboré 01',
            'Tamboré 02',
            'Tamboré 03',
            'Tamboré 04',
            'Tamboré 05',
            'Tamboré 06',
            'Tamboré 07',
            'Tamboré 10',
            'Tamboré 11',
            'Terraços Tamboré',
            'The Garden',
            'The Lake',
            'The Penthouses Tamboré',
            'Top Village',
            'Trix Housing Tamboré',
            'Único Alphaville',
            'Uptown Housing',
            'Valville 1',
            'Valville 2',
            'Vedara',
            'Verone',
            'Verte Ville',
            'Vila Velha',
            'Villa Solaia',
            'Village',
            'Vista Alta',
            'Vogue',
            'Wave',
            'West Gate',
            'West Side',
            'Wi House',
            'Win Alphaville',
        ];

        DB::table('condominiums')->insert(
            collect($names)
                ->values()
                ->map(fn (string $name, int $index) => [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->all()
        );

        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('condominium_id')->nullable()->after('tipo_propriedade_id')->constrained('condominiums')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('condominium_id');
        });

        Schema::dropIfExists('condominiums');
    }
};
