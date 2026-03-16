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
        Schema::create('movie_sessions', function (Blueprint $table) {
            $table->id();
            $table->dateTime("start_at");
            $table->dateTime("end_at");
            $table->enum('language', [
                'en',  // English
                'zh',  // Chinese (Mandarin)
                'hi',  // Hindi
                'es',  // Spanish
                'fr',  // French
                'ar',  // Arabic
                'bn',  // Bengali
                'pt',  // Portuguese
                'ru',  // Russian
                'ur',  // Urdu
                'de'   // German
            ])->default('en');
            $table->float("price");

            $table->unsignedBigInteger("room_id");
            $table->unsignedBigInteger("movie_id");

            $table->foreign("movie_id")
                  ->references("id")
                  ->on("movies")
                  ->onDelete("cascade");

            $table->foreign("room_id")
                  ->references("id")
                  ->on("rooms")
                  ->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_sessions');
    }
};
