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
        Schema::create('task_events', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('task_id');
            $table->string('event_type');
            $table->json('payload');
            $table->timestamp('occurred_on');

            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_events');
    }
};
