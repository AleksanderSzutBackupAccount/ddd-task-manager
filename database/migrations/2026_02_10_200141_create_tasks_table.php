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
        Schema::create('tasks', static function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->uuid('project_id');
            $table->string('name');
            $table->text('description');
            $table->string('status'); // To Do, In Progress, Done
            $table->string('assigned_user_id')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
