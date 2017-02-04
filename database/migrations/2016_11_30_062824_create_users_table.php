<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid');
            $table->integer('club_id');
            $table->string('role');
            $table->string('name');
            $table->string('passwd');
            $table->string('phone');
            $table->string('photo');
            $table->string('state');
            $table->double('amount')->default(0);
            $table->double('point')->default(0);
            $table->double('consumption')->default(0);
            $table->rememberToken();
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
