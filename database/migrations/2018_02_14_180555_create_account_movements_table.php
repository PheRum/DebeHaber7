<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountMovementsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('account_movements', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedInteger('taxpayer_id');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->string('partner_taxid', 15)->nullable();
            $table->string('partner_name')->nullable();

            $table->unsignedInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

            $table->string('currency', 3)->default('USD');
            $table->unsignedDecimal('rate', 10, 4)->default(1);

            $table->date('date');

            $table->unsignedDecimal('debit', 18, 2)->default(0);
            $table->unsignedDecimal('credit', 18, 2)->default(0);

            $table->string('comment')->nullable();

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
        Schema::dropIfExists('account_movements');
    }
}
