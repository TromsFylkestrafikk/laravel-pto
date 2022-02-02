<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PtoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pto_company', function (Blueprint $table)
        {
            $table->integer('id');
            $table->string('name', 60);
        });

        Schema::create('pto_vehicle', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment("The vehicle ID as received from SIRI VM dumps");
            $table->string('internal_id', 8)->nullable()->comment("Company internal id of vehicle.");
            $table->string('type', 16)->comment("Vehicle type (bus, hsc, ferry)");
            $table->unsignedMediumInteger('company_id');
            $table->boolean('apc_enabled')->default(false)->comment("Do we have APC onboard? Is it calibrated enough for everyday use?");
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('pto_vehicle_bus', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment("The vehicle ID as received from SIRI VM dumps");
            $table->string('registration_id', 32)->nullable();
            $table->char('registration_year', 4)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('class', 100);
            $table->unsignedMediumInteger('capacity_pax')->default(0);
            $table->unsignedMediumInteger('capacity_pax_avail')->default(0);
            $table->unsignedMediumInteger('capacity_seats')->nullable();
            $table->unsignedMediumInteger('capacity_seats_avail')->nullable();
            $table->unsignedMediumInteger('capacity_stands')->nullable();
            $table->unsignedMediumInteger('capacity_stands_avail')->nullable();
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('pto_vehicle_watercraft', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment("The vehicle ID as received from SIRI VM dumps");
            $table->unsignedInteger('imo')->nullable()->comment("International Maritime Organization number");
            $table->string('type', 16);
            $table->char('prefix', 8);
            $table->string('name', 128);
            $table->char('callsign', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('line', 32)->comment("Line numbers separated with comma this vessel primary services.");
            $table->unsignedMediumInteger('capacity_pax')->default(0);
            $table->unsignedMediumInteger('capacity_pax_avail')->default(0);
            $table->unsignedMediumInteger('capacity_cars')->nullable();
            $table->unsignedMediumInteger('capacity_cars_avail')->nullable();
            $table->string('url', 256)->nullable()->comment("Useful for watercraft geeks in need of more information");
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pto_company');
        Schema::dropIfExists('pto_vehicle');
        Schema::dropIfExists('pto_vehicle_bus');
        Schema::dropIfExists('pto_vehicle_watercraft');
    }
}
