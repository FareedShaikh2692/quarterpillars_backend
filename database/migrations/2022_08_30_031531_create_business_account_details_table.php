<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessAccountDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_account_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('business_id');
            $table->string('gst_number');
            $table->string('pan_number');
            $table->string('bank_account_number');
            $table->string('bank_account_holder_name');
            $table->string('bank_ifsc_code');
            $table->string('bank_branch');
            $table->text('product_or_service_details')->nullable();
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
        Schema::dropIfExists('business_account_details');
    }
}
