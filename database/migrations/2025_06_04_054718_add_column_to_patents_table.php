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
            $table->date('downgraded_to_utility_model_at')->nullable()->after('images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patents', function (Blueprint $table) {
            $table->dropColumn('downgraded_to_utility_model_at');
        });
    }
};
