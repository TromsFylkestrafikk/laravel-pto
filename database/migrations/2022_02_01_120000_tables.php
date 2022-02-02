<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_vehicle', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment("The vehicle ID as received in SIRI VM dumps");
            $table->string('internal_id', 8)->nullable()->comment("Company internal id of vehicle.");
            $table->string('type', 16)->comment("Vehicle type (bus, hsc, ferry)");
            $table->unsignedMediumInteger('company_id');
            $table->boolean('apc_enabled')->default(false)->comment("Do we have APC onboard? Is it calibrated enough for everyday use?");
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('pto_company', function (Blueprint $table)
        {
            $table->integer('id');
            $table->string('name', 60);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pto_vehicle');
        Schema::dropIfExists('pto_company');
    }
}
