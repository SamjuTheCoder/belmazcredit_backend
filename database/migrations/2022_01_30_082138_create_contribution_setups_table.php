<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributionSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_setups', function (Blueprint $table) {
            $table->id();
            $table->integer('phases_id')->unique();
            $table->double('contributed_amount')->unique();
            $table->double('receive')->unique();
            $table->double('withdrawal')->unique();
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
        Schema::dropIfExists('contribution_setups');
    }
}
