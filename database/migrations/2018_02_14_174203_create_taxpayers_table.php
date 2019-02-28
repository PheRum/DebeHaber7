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

            // $table->increments('id');
            $table->uuid('id')->primary();

            $table->string('country', 3)->default('PRY');
            $table->string('currency', 3)->default('PYG')->nullable();

            $table->string('taxid');
            $table->unsignedTinyInteger('code')->nullable();

            $table->string('name', 255);
            $table->string('alias', 32)->nullable();

            $table->string('address')->nullable();
            $table->string('telephone', 64)->nullable();
            $table->string('email', 64)->nullable();

            $table->unsignedTinyInteger('type')->nullable();
            $table->unsignedTinyInteger('regime_type')->nullable();

            $table->string('agent_name', 64)->nullable();
            $table->string('agent_taxid', 32)->nullable();

            $table->boolean('is_company')->default(false);
            $table->boolean('show_inventory')->default(false);
            $table->boolean('show_production')->default(false);
            $table->boolean('show_fixedasset')->default(false);

            $table->boolean('does_import')->default(false);
            $table->boolean('does_export')->default(false);

            $table->timestamps();
            $table->softDeletes();
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
