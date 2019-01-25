<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedInteger('cycle_id');
            $table->foreign('cycle_id')->references('id')->on('cycles')->onDelete('cascade');

            $table->unsignedMediumInteger('number')->nullable();

            $table->date('date');

            $table->string('comment', 64);

            $table->boolean('is_automatic')->default(false)->comment('helps identify the transactions made by user');
            $table->boolean('is_presented')->default(false)->comment('If Journal has been presented and now allow further changes.');
            $table->boolean('is_first')->default(false)->comment('Refers to if Journal is an Opening Balance. Can only be one per Accounting Cycle');
            $table->boolean('is_last')->default(false)->comment('Refers to if Journal is an Closing Balance. Can only be one per Accounting Cycle');;

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
        Schema::dropIfExists('journals');
    }
}
