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
        Schema::table('licenses', function (Blueprint $table) {
            // Modify the status enum to include all required statuses
            $table->dropColumn('status');
        });
        
        Schema::table('licenses', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'expired', 'disabled', 'suspended'])->default('active')->after('license_key');
            $table->integer('max_activations')->default(1)->after('expires_at');
            $table->text('notes')->nullable()->after('max_activations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['max_activations', 'notes']);
            $table->dropColumn('status');
        });
        
        Schema::table('licenses', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active')->after('license_key');
        });
    }
};
 