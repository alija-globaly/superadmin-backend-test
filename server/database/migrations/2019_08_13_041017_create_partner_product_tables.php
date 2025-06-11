<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create partners table
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('fax')->nullable();
            $table->string('currency_code');
            $table->string('logo')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedInteger('country');
            $table->unsignedInteger('category_id');
            $table->timestamps();
        });

        // Create products table
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('partner_id')->index();
            $table->json('intake_month')->nullable();
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });

        // Create partner_branches table
        Schema::create('partner_branches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->string('email');
            $table->string('type');
            $table->string('street')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->integer('country')->nullable();
            $table->unsignedInteger('partner_id')->index();
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });

        // Create branches_products table
        Schema::create('branches_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('partner_branches')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches_products');
        Schema::dropIfExists('partner_branches');
        Schema::dropIfExists('products');
        Schema::dropIfExists('partners');
    }
}
