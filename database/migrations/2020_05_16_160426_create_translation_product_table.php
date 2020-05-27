<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_products', function (Blueprint $table) {
            $table->unsignedBigInteger('related_id');
            $table->string('related_name');
            $table->string('en')->nullable();
            $table->string('ru')->nullable();

            $table->primary(['related_id','related_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translation_products');
    }
}
