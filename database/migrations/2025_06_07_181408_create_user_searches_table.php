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
        Schema::create('user_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamp('searched_at')->useCurrent();
             $table->timestamps();
            $table->index(['user_id', 'searched_at']); // Para búsquedas rápidas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_searches');
    }
};
