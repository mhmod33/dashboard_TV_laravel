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
        Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->string('serial_number');
        $table->string('payment_id')->unique();
        $table->string('owner');
        $table->string('customer_name');
        $table->date('date');
        $table->string('duration');
        $table->date('exp_before')->nullable();
        $table->date('exp_after')->nullable();
        $table->decimal('cost', 8, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
