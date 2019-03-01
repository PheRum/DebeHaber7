<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('charts')->onDelete('cascade');

            $table->unsignedInteger('chart_version_id');
            $table->foreign('chart_version_id')->references('id')->on('chart_versions')->onDelete('cascade');

            $table->unsignedInteger('taxpayer_id')->nullable();
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->string('country', 3)->default('PRY');

            $table->boolean('is_accountable')->default(true);

            $table->string('code');
            $table->string('name');
            $table->unsignedTinyInteger('level')->default(1);

            $table->unsignedTinyInteger('type')->default(1)->index()
            ->comment('
            1 = Asset
            2 = Liabilities
            3 = Capital
            4 = Income
            5 = Expense
            ');

            $table->unsignedTinyInteger('sub_type')->nullable()->index()
            ->comment('1 = Cash and Bank Accounts
            2 = Accounts Receivable
            3 = Undeposited Funds
            4 = Inventory
            5 = Fixed Assets Groups
            6 = Prepaid Insurance
            7 = Sales Tax Credit

            8 = Accrued Liablities
            9 = Accounts Payable
            10 = Payroll liabilities
            11 = Notes Payable

            ... More to come.
            ');

            $table->string('partner_taxid', 15)->nullable();
            $table->string('partner_name')->nullable();

            $table->unsignedDecimal('coefficient', 4, 4)->nullable();
            $table->unsignedDecimal('asset_years', 4, 2)->nullable();

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
        Schema::dropIfExists('charts');
    }
}
