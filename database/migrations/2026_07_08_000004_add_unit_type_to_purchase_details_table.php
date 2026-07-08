<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds unit_type to existing purchase_details tables. Fresh installs already
     * get the column from 2026_07_08_000001, so guard against a duplicate add.
     */
    public function up(): void
    {
        if (Schema::hasColumn('purchase_details', 'unit_type')) {
            return;
        }

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->string('unit_type')->default('primary')->after('quantity');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('purchase_details', 'unit_type')) {
            return;
        }

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('unit_type');
        });
    }
};
