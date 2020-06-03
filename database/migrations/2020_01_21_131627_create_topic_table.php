<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idSubject')->unsigned()
                ->foreign('idSubject_topic')
                ->references('id')->on('subjects')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('name',50);
	    $table->boolean('isTask');
            $table->enum('state',['0','1','2','3']);
      	    $table->enum('priority',['0','1','2']);
	    $table->string('notes',250)->nullable();
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
        Schema::dropIfExists('topics');
    }
}
