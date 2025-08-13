<?php

use App\Enums\PatentStatusEnum;
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
        Schema::create('patents', function (Blueprint $table) {
            $table->id();
            $table->string('invention',500)->nullable();
            $table->string('slug',600)->unique();
            $table->string('inventors',500)->nullable();
            $table->text('description')->nullable();
            $table->string('patent_number')->nullable();
            $table->date('filing_date')->nullable();
            $table->date('publication_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('status')->default(PatentStatusEnum::SUBMITTED_UITSO);
            $table->json('images')->nullable();
            $table->timestamps();
            $table->index('invention');
            $table->index('inventors');
            $table->index('publication_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patents');
    }
};
