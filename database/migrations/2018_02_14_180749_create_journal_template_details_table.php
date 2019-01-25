<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalTemplateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_template_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('journal_template_id');
            $table->foreign('journal_template_id')->references('id')->on('journal_templates')->onDelete('cascade');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedDecimal('debit_coef', 4, 4)->default(0);
            $table->unsignedDecimal('credit_coef', 4, 4)->default(0);

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
        Schema::dropIfExists('journal_template_details');
    }
}
