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
        Schema::create('domain_event', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('aggregate_id')->index();
            $table->integer('version');
            $table->string('name');
            $table->json('payload');
            $table->timestamp('occurred_on');

            $table->unique(['aggregate_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_event');
    }
};
