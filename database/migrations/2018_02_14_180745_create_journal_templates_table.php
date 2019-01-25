<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_templates', function (Blueprint $table) {
            $table->increments('id');

            $table->string('country', 3)->default('PRY');

            $table->unsignedInteger('taxpayer_id')->nullable();
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->string('name');

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
        Schema::dropIfExists('journal_templates');
    }
}
