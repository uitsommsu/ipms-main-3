<?php

use App\Models\User;
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
        Schema::create('assistance_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->text('inquiry')->nullable();
            $table->text('response')->nullable();
            $table->boolean('is_responded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_forms');
    }
};
