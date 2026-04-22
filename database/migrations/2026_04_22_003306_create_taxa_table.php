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
        Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('rank')->nullable();
            $table->string('parent_name')->nullable();
            $table->timestamps();

            // B-tree index on name supports prefix-match autocomplete (LIKE 'query%')
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxa');
    }
};
