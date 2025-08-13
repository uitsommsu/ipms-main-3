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
            $table->unsignedBigInteger('original_patent_id')->nullable()->after('images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utility_models', function (Blueprint $table) {
            $table->dropColumn('original_patent_id');
        });
    }
};
