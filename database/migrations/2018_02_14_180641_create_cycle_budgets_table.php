<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCycleBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_budgets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('cycle_id');
            $table->foreign('cycle_id')->references('id')->on('cycles')->onDelete('cascade');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedDecimal('debit', 18, 2)->default(0);
            $table->unsignedDecimal('credit', 18, 2)->default(0);

            $table->string('comment');

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
        Schema::dropIfExists('cycle_budgets');
    }
}
