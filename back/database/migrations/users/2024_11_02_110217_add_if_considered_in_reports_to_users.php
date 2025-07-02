<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('if_considered_in_reports')->default(true);
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        });
    }
};
