<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAadhaarVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aadhaar_verifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('business_id');
            $table->integer('aadhaar_number');
            $table->boolean('is_aadhaar_verified')->nullable();
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
        Schema::dropIfExists('aadhaar_verifications');
    }
}
