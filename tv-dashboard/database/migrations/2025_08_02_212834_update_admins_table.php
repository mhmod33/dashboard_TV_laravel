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
            DB::statement("alter table admins modify role ENUM('admin', 'subadmin', 'superadmin') DEFAULT 'admin'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE admins MODIFY role ENUM('admin', 'superadmin') DEFAULT 'admin'");
    }
};
