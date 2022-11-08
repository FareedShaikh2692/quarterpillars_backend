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
            $table->tinyInteger('product_category');
            $table->string('product_image')->nullable();
            $table->string('product_video_url')->nullable();
            $table->string('product_name', 120);
            $table->string('product_brand', 120)->nullable();
            $table->tinyInteger('unit_id');
            $table->tinyInteger('minimum_qty');
            $table->text('product_tags')->nullable();
            $table->boolean('is_refundable');
            $table->boolean('is_cod');
            $table->text('product_description');
            $table->float('unit_price');
            $table->float('sales_price');
            $table->float('dicount')->nullable();
            $table->tinyInteger('product_type');
            $table->tinyInteger('color_id')->nullable();
            $table->tinyInteger('size_id')->nullable();
            $table->tinyInteger('units_id')->nullable();
            $table->tinyInteger('qty');
            $table->tinyInteger('warning_qty')->nullable();
            $table->float('product_tax')->nullable();
            $table->tinyInteger('tax_type')->nullable();
            $table->string('service_company');
            $table->tinyInteger('delivery_type_id');
            $table->integer('pin_code');
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
        Schema::dropIfExists('products');
    }
}
