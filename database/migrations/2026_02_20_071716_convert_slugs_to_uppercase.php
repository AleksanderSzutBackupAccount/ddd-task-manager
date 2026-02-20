<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('projects')->update(['slug' => DB::raw('UPPER(slug)')]);
        DB::table('tasks')->update(['slug' => DB::raw('UPPER(slug)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('projects')->update(['slug' => DB::raw('LOWER(slug)')]);
        DB::table('tasks')->update(['slug' => DB::raw('LOWER(slug)')]);
    }
};
