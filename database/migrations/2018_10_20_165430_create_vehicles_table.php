<?php

use App\Vehicle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id'); $table->primary('id');
            $table->string('brand');
            $table->string('model');
            $table->year('purchase_year');
            $table->unsignedDecimal('purchase_price', 8, 2);
            $table->timestamps();
        });
        /*Vehicle::create([
            'name' => 'Tesla',
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
