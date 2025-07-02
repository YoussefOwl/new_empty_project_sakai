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
        Schema::create('config_can_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_can_access_key");
            $table->unsignedBigInteger("id_role");
            $table->timestamps();
            $table->foreign('id_can_access_key')->references('id')->on('config_can_access_keys');
            $table->foreign('id_role')->references('id')->on('roles');
            $table->unique(['id_can_access_key', 'id_role'], 'access_key_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_can_access');
    }
};
