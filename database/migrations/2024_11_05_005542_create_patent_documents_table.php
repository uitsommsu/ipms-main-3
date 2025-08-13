<?php

use App\Models\Patent;
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
        Schema::create('patent_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Patent::class)->constrained()->cascadeOnDelete();
            $table->tinyInteger('document_type')->nullable();
            $table->string('filename')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('comments')->nullable();
            $table->date('commented_at')->nullable();
            $table->tinyInteger('revision_history')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patent_documents');
    }
};
