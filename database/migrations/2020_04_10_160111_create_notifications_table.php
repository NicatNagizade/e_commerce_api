<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedTinyInteger('type')->default(0);
            $table->unsignedBigInteger('related_id')->default(0);
            $table->string('title');
            $table->string('content');
            $table->string('note')->nullable();
            $table->dateTime('seen')->nullable();
            $table->dateTime('created_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
