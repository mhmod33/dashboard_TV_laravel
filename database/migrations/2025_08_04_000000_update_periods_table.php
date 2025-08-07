<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->string('period_code')->unique()->after('id');
            $table->string('display_name')->after('period_code');
            $table->integer('months')->after('display_name');
            $table->integer('days')->after('months');
            $table->integer('display_order')->default(1)->after('days');
            $table->boolean('active')->default(true)->after('display_order');
            $table->decimal('price', 10, 2)->default(0)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'period_code', 'display_name', 'months', 'days', 'display_order', 'active', 'price'
            ]);
        });
    }
};