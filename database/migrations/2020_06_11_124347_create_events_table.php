<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
           $table->bigIncrements('id');

	   $table->unsignedBigInteger('idUser')->nullable();

           $table->foreign('idUser')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('event_name',50);
	    $table->string('event_resume',50)->nullable;
            $table->date('event_date');

	    $table->unsignedBigInteger('idSubject')->nullable;


            $table->foreign('idSubject')
                    ->references('id')
                    ->on('subjects')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');


	    $table->integer('event_color');
	    $table->integer('event_iconId');
	    $table->boolean('appnotification');
	    $table->string('event_notes',250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
