<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_period_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('period_id');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('plan', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');
            $table->unique(['admin_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_period_overrides');
    }
};


