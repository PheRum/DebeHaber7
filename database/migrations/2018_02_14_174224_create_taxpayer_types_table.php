<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxpayerTypesTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('taxpayer_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('taxpayer_id')->unsigned()->comment('Taxpayer to use this CompanyType');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');
            $table->tinyInteger('type')->unsigned();
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
        Schema::dropIfExists('taxpayer_types');
    }
}
