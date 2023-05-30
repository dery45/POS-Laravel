<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->decimal('low_price', 10, 2);
            $table->decimal('stock_price', 10, 2);
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('fk_product_id')->nullable();
            $table->timestamps();
    
            $table->foreign('fk_product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
    

    public function down()
    {
        Schema::table('price_histories', function (Blueprint $table) {
            $table->dropForeign(['fk_product_id']);
        });

        Schema::dropIfExists('price_histories');
    }
}


