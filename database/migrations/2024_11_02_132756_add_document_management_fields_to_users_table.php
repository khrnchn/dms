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
        Schema::table('users', function (Blueprint $table) {
            // Add role column after email
            $table->enum('role', [
                'system_admin',
                'manager',
                'file_admin',
                'staff'
            ])->after('email')->default('staff');

            // Add department relationship
            $table->foreignId('department_id')
                ->nullable()
                ->after('role')
                ->constrained('departments')
                ->nullOnDelete();

            // Add status and activity tracking
            $table->boolean('is_active')
                ->after('department_id')
                ->default(true);

            $table->timestamp('last_login_at')
                ->after('is_active')
                ->nullable();

            // Add soft deletes for user archiving
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove added columns
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'role',
                'department_id',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};
