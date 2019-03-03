<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixedAssetsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedInteger('taxpayer_id');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->string('currency', 3)->default('USD');
            $table->unsignedDecimal('rate', 10, 4)->default(1);

            $table->string('serial')->nullable();

            $table->string('name');

            $table->date('purchase_date');

            $table->unsignedDecimal('purchase_value', 18, 2)->default(0);

            $table->unsignedDecimal('current_value', 18, 2)->default(0);

            $table->unsignedDecimal('quantity', 10, 2)->default(1);

            $table->date('sales_date')->nullable();

            $table->date('sync_date')->nullable();

            $table->timestamps();

            $table->date('depreciated_at')->nullable();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('fixed_assets');
    }
}
