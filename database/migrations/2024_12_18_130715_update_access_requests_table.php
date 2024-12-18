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
        Schema::table('access_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            $table->unsignedBigInteger('file_admin_approved_by')->nullable();
            $table->timestamp('file_admin_approved_at')->nullable();

            $table->foreign('manager_approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('file_admin_approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests'); 
    }
};
