<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_role');
            $table->integer('has_access'); // 1 Autorisé // 2 Non autorisé
            $table->string('nom', 50);
            $table->string('prenom', 50);
            $table->string('email', 80)->unique();
            $table->text('password');
            $table->integer('if_has_sound')->default(1);
            $table->text('image_user')->nullable(); // le chemin de l'image
            /* -------------------------- les clées étrangères -------------------------- */
            $table->foreign('id_role')->references('id')->on('roles');
            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
