<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('role');
        });

        Schema::table('admins', function (Blueprint $table) {
            // Add the new enum column with updated values
            $table->enum('role', ['superadmin', 'admin', 'subadmin'])->default('admin')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('role');
        });

        Schema::table('admins', function (Blueprint $table) {
            // Restore the original enum column
            $table->enum('role', ['admin', 'superadmin'])->default('admin')->after('status');
        });
    }
}; 