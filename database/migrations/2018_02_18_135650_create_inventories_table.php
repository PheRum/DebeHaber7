<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table)
        {
            $table->increments('id');

            $table->unsignedInteger('taxpayer_id');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');


            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedDecimal('sales_value', 18, 2);
            $table->unsignedDecimal('cost_value', 18, 2);
            $table->unsignedDecimal('inventory_value', 18, 2);
            $table->string('chart_of_incomes');
            $table->string('comments');

            $table->unsignedDecimal('current_value', 18, 2);
            
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
        Schema::dropIfExists('inventories');
    }
}
