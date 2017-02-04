<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('province');
            $table->string('city');
            $table->string('location');
            $table->string('phone');
            $table->string('photo');
            $table->double('consumption')->default(0);
            $table->string('code')->default(null);
            //$table->double('point_back')->default(1);
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
        //
    }
}
