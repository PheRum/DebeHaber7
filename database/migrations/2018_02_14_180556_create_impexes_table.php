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

            $table->string('partner_taxid', 15)->nullable();
            $table->string('partner_name')->nullable();

            $table->string('currency', 3)->default('USD');
            $table->unsignedDecimal('rate', 10, 4)->default(1);

            $table->boolean('is_import')->default(true)->comment('determines if impex is import or export related');

            $table->string('type', 4)->default('FOB');

            $table->date('date');

            $table->string('code');

            $table->string('comment');

            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('impex_id')->nullable()->after('document_id');
            $table->foreign('impex_id')->references('id')->on('impexes')->onDelete('cascade');
        });

        Schema::create('impex_expenses', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();

            $table->unsignedInteger('impex_id');
            $table->foreign('impex_id')->references('id')->on('impexes')->onDelete('cascade');

            $table->unsignedInteger('transaction_detail_id')->nullable();
            $table->foreign('transaction_detail_id')->references('id')->on('transaction_details')->onDelete('cascade');

            $table->unsignedInteger('chart_id')->nullable();
            $table->foreign('chart_id')->references('id')->on('charts')->onDelete('cascade');

            $table->string('currency', 3)->default('USD');
            $table->unsignedDecimal('rate', 10, 4)->default(1);

            $table->unsignedDecimal('value', 10, 4)->default(0);

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

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_impex_id_foreign');
            $table->dropColumn('impex_id');
        });

        Schema::dropIfExists('impexes');
    }
}
