<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->string('password');
            $table->integer('role_id')->default(0);
            $table->integer('phases_id')->nullable();
            $table->integer('investment_id')->default(0);
            $table->string('referral_id')->nullable();
            $table->string('transaction_code');
            $table->string('sponsor_id')->nullable();
            $table->integer('phase1_status')->default(0);
            $table->integer('phase2_status')->default(0);
            $table->integer('phase3_status')->default(0);
            $table->integer('phase4_status')->default(0);
            $table->string('group_id')->nullable();
            $table->integer('order_id')->default(0);
            $table->integer('payment_status')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('users');
    }
}
