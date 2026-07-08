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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('purchase_no')->unique();
            $table->dateTime('purchase_date')->index();
            $table->string('purchase_status')->default('pending')->index();
            $table->decimal('sub_total', 15, 2);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('pay_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0); // Supplier Debt (Hutang)
            $table->timestamps();
        });

        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('unit_type')->default('primary'); // 'primary' or 'secondary', needed to convert stock on later receive
            $table->decimal('unit_cost', 15, 2);
            // NOTE: unit_type added here for fresh installs; existing DBs get it via 2026_07_08_000004.
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('type')->index(); // 'addition' (stock in) or 'subtraction' (stock out)
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->foreignId('adjusted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('product_returns', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // 'sales' or 'purchase'
            $table->string('reference_no')->index(); // Invoice No or Purchase No
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_returns');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('purchase_details');
        Schema::dropIfExists('purchases');
    }
};
