<?php

use App\Enums\UtilityModelStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('utility_models', function (Blueprint $table) {
            $table->id();
            $table->string('title',500)->nullable();
            $table->string('slug',600)->unique();
            $table->string('researchers',500)->nullable();
            $table->text('description')->nullable();
            $table->string('um_number')->nullable();
            $table->date('filing_date')->nullable();
            $table->date('publication_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('status')->default(UtilityModelStatusEnum::SUBMITTED_UITSO);
            $table->json('images')->nullable();
            $table->timestamps();
            $table->index('title');
            $table->index('slug');
            $table->index('researchers');
            $table->index('publication_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_models');
    }
};
