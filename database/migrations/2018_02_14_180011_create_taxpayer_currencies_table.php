<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxpayerCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxpayer_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('taxpayer_id')->comment('Taxpayer to use this Currency for Transactions');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');
            
            $table->string('currency', 3)->default('USD');
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
        Schema::dropIfExists('taxpayer_currencies');
    }
}
