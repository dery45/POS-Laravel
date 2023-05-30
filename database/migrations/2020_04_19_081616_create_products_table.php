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
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_product')->nullable(); // Make the foreign key nullable
            $table->string('image')->nullable();
            $table->string('barcode');
            $table->boolean('status')->default(1);
            $table->unsignedInteger('minimum_low');
            $table->string('brand')->nullable();
            $table->decimal('low_price', 10, 2)->nullable();
            $table->decimal('stock_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // If the table already exists, update the foreign key
            if (Schema::hasTable('categories')) {
                $table->foreign('category_product')->references('id')->on('categories')->onDelete('cascade');
            }
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
