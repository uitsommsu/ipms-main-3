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
        Schema::table('patents', function (Blueprint $table) {
            $table->date('withdrawn_at')->nullable()->after('original_utility_model_id');
            $table->date('abandoned_at')->nullable()->after('withdrawn_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patents', function (Blueprint $table) {
            $table->dropColumn(['withdrawn_at', 'abandoned_at']);
        });
    }
};
