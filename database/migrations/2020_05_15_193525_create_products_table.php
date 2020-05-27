<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price',8,2,true);
            $table->string('image')->nullable();
            $table->enum('gender',['male','female','unisex'])->default('male');
            $table->boolean('child')->default(0);
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->unsignedBigInteger('manufacturer_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->unsignedBigInteger('sub_product_type_id');
            $table->timestamps();

            $table->foreign('manufacturer_id')->on('manufacturers')->references('id')->onDelete('CASCADE');
            $table->foreign('sub_category_id')->on('sub_categories')->references('id')->onDelete('CASCADE');
            $table->foreign('sub_product_type_id')->on('sub_product_types')->references('id')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
