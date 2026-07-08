<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('primary_unit')->default('pcs')->after('stock');
            $table->string('secondary_unit')->nullable()->after('primary_unit'); // e.g. 'box', 'dus'
            $table->integer('conversion_rate')->default(1)->after('secondary_unit'); // e.g. 1 box = 12 pcs
            $table->decimal('wholesale_price', 15, 2)->nullable()->after('selling_price');
            $table->integer('wholesale_minimum_qty')->default(0)->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'primary_unit',
                'secondary_unit',
                'conversion_rate',
                'wholesale_price',
                'wholesale_minimum_qty',
            ]);
        });
    }
};
