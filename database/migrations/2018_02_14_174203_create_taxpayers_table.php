<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxpayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxpayers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country', 3)->default('PRY');
            $table->string('taxid')->index();
            $table->unsignedTinyInteger('code')->nullable();

            $table->string('name', 255);
            $table->string('alias', 32)->nullable();

            $table->string('address')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email', 64)->nullable();
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
        Schema::dropIfExists('taxpayers');
    }
}
