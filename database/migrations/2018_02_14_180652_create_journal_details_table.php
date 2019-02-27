<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('journal_id');
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');

            $table->unsignedInteger('chart_id');
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedDecimal('debit', 18, 2)->default(0);
            $table->unsignedDecimal('credit', 18, 2)->default(0);

            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('journal_id')->nullable()->after('type');
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->unsignedInteger('journal_id')->nullable()->after('taxpayer_id');
        });

        Schema::table('account_movements', function (Blueprint $table) {
            $table->unsignedInteger('journal_id')->nullable()->after('taxpayer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('journal_id');
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->dropColumn('journal_id');
        });

        Schema::table('account_movements', function (Blueprint $table) {
            $table->dropColumn('journal_id');
        });

        Schema::dropIfExists('journal_details');
    }
}
