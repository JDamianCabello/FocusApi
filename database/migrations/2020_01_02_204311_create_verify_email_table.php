<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifyEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verify_mails', function (Blueprint $table) {
	    $table->bigInteger('idUser')->unsigned()
                ->foreign('idUser')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade')
		->primary();

	    $table->char('verification_code',8);
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
        Schema::dropIfExists('verify_email');
    }
}
