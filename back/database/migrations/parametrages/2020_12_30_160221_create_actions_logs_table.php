<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsLogsTable extends Migration
{
    public function up()
    {
        Schema::create('actions_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_user'); // utilisateur qui a fait l'action
            $table->string('libelle_log',255);
            $table->string('table_name',255);
            $table->longText('description')->nullable();
            $table->json('json_log_data')->nullable();
            $table->timestamps();
            /* --------------------------- les clés étrangères -------------------------- */
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actions_logs');
    }
}
