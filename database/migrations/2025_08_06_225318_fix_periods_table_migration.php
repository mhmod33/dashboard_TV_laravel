<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing records with empty period_code to have a default value
        DB::table('periods')->whereNull('period_code')->orWhere('period_code', '')->update([
            'period_code' => DB::raw("CONCAT('PERIOD_', id)")
        ]);
        
        // Now add the columns from the update_periods_table migration
        Schema::table('periods', function (Blueprint $table) {
            if (!Schema::hasColumn('periods', 'period_code')) {
                $table->string('period_code')->unique()->after('id');
            }
            
            if (!Schema::hasColumn('periods', 'display_name')) {
                $table->string('display_name')->after('period_code');
            }
            
            if (!Schema::hasColumn('periods', 'months')) {
                $table->integer('months')->after('display_name');
            }
            
            if (!Schema::hasColumn('periods', 'days')) {
                $table->integer('days')->after('months');
            }
            
            if (!Schema::hasColumn('periods', 'display_order')) {
                $table->integer('display_order')->default(1)->after('days');
            }
            
            if (!Schema::hasColumn('periods', 'active')) {
                $table->boolean('active')->default(true)->after('display_order');
            }
            
            if (!Schema::hasColumn('periods', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn([
                'period_code', 'display_name', 'months', 'days', 'display_order', 'active', 'price'
            ]);
        });
    }
};
