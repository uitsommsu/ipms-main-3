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
        Schema::table('utility_models', function (Blueprint $table) {
            $table->date('withdrawn_at')->nullable()->after('upgraded_to_patent_at');
            $table->date('abandoned_at')->nullable()->after('withdrawn_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_models', function (Blueprint $table) {
            $table->dropColumn(['withdrawn_at', 'abandoned_at']);
        });
    }
};
