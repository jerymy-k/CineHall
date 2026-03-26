<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movie_sessions', function (Blueprint $table) {
            $table->enum('type', ['normal', 'vip'])->default('normal')->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('movie_sessions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

