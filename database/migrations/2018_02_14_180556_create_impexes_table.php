<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impexes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('taxpayer_id');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->boolean('is_import')->default(true)->comment('determines if impex is import or export related');
            $table->string('type', 4)->default('FOB');
            $table->date('date');
            $table->string('currency', 3)->default('USD');
            $table->unsignedDecimal('rate')->default(1);
            $table->string('comment');

            $table->timestamps();
        });

        Schema::create('impex_invoices', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();

            $table->unsignedInteger('impex_id');
            $table->foreign('impex_id')->references('id')->on('impexes')->onDelete('cascade');

            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('impex_expenses', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();

            $table->unsignedInteger('impex_id');
            $table->foreign('impex_id')->references('id')->on('impexes')->onDelete('cascade');

            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

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
        Schema::dropIfExists('impex_expenses');
        Schema::dropIfExists('impex_invoices');
        Schema::dropIfExists('impexes');
    }
}
