<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('phases_id');
            $table->string('referral_id');
            $table->string('sponsor_id')->nullable();
            $table->double('contribution_amount');
            $table->integer('receive_status')->default(0);
            $table->integer('phase_status')->default(0);
            $table->string('group_id');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('contributions');
    }
}
