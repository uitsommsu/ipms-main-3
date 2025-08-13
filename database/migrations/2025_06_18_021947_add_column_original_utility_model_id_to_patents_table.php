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
            $table->unsignedBigInteger('original_utility_model_id')->nullable()->after('downgraded_to_utility_model_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patents', function (Blueprint $table) {
            $table->dropColumn('original_utility_model_id');
        });
    }
};
