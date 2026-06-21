<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('aceita_permuta')->default(false);
            $table->integer('lavabos')->nullable();
            $table->integer('andar')->nullable();
            $table->decimal('area_construida', 12, 2)->nullable();
            $table->decimal('valor_condominio', 12, 2)->nullable();
            $table->decimal('valor_iptu', 12, 2)->nullable();
            $table->boolean('mobiliado')->default(false);
            $table->boolean('aceita_financiamento')->default(false);
            $table->integer('ano_construcao')->nullable();
            $table->string('posicao_solar')->nullable();
        });

        DB::table('properties')
            ->whereNull('valor_condominio')
            ->update([
                'valor_condominio' => DB::raw('condominio'),
            ]);

        DB::table('properties')
            ->whereNull('valor_iptu')
            ->update([
                'valor_iptu' => DB::raw('iptu'),
            ]);
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'aceita_permuta',
                'lavabos',
                'andar',
                'area_construida',
                'valor_condominio',
                'valor_iptu',
                'mobiliado',
                'aceita_financiamento',
                'ano_construcao',
                'posicao_solar',
            ]);
        });
    }
};
