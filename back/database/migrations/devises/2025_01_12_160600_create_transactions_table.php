<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_user_createur");
            $table->unsignedBigInteger("id_devise");
            $table->double("prix");
            $table->double("taux");
            $table->boolean("is_entree");
            $table->string("name_client")->nullable();
            $table->text("description")->nullable();
            $table->foreign("id_user_createur")->references("id")->on("users");
            $table->foreign("id_devise")->references('id')->on("devises");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
