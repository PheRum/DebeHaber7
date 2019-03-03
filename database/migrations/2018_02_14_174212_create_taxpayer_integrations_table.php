<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxpayerIntegrationsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('taxpayer_integrations', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('taxpayer_id');
            $table->foreign('taxpayer_id')->references('id')->on('taxpayers')->onDelete('cascade');

            $table->unsignedInteger('team_id')->unsigned();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->unsignedTinyInteger('type')->comment('1 = Company. 2 = Accountant. 3 = Auditor');

            $table->boolean('is_owner')->default(false);

            $table->boolean('notification_monthly')->default(false);
            $table->boolean('notification_quarterly')->default(false);
            $table->boolean('notification_semesterly')->default(false);
            $table->boolean('notification_yearly')->default(false);
            $table->boolean('notification_sync')->default(true);

            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected, 4 = Archive');

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
        Schema::dropIfExists('taxpayer_integrations');
    }
}
