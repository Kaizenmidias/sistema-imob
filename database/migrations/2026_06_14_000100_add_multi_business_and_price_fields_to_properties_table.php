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
            $table->boolean('aceita_venda')->default(false)->after('business_type_id');
            $table->boolean('aceita_locacao')->default(false)->after('aceita_venda');
            $table->boolean('aceita_temporada')->default(false)->after('aceita_locacao');
            $table->decimal('valor_venda', 15, 2)->nullable()->after('valor');
            $table->decimal('valor_locacao', 15, 2)->nullable()->after('valor_venda');
        });

        DB::table('properties')
            ->select(['id', 'operacao', 'valor'])
            ->orderBy('id')
            ->chunkById(200, function ($properties): void {
                foreach ($properties as $property) {
                    $operation = trim((string) ($property->operacao ?? ''));
                    $price = $property->valor !== null ? (float) $property->valor : null;

                    DB::table('properties')
                        ->where('id', $property->id)
                        ->update([
                            'aceita_venda' => $operation === 'Venda',
                            'aceita_locacao' => $operation === 'Aluguel',
                            'aceita_temporada' => $operation === 'Temporada',
                            'valor_venda' => $operation === 'Venda' ? $price : null,
                            'valor_locacao' => $operation === 'Aluguel' ? $price : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'aceita_venda',
                'aceita_locacao',
                'aceita_temporada',
                'valor_venda',
                'valor_locacao',
            ]);
        });
    }
};
