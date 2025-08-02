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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('customer_name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('plan_id')->constrained('periods')->onDelete('cascade');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('status', ['active', 'expired'])->default('active');
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
