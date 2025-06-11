<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('academic_requirement')->nullable();
            $table->json('english_test_score')->nullable();
            $table->json('other_test_score')->nullable();
            $table->json('fees')->nullable();
            $table->softDeletes();

            $table->index('name');
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->string('registration_number')->nullable();
            $table->dateTime('approved_at')->nullable()->index();
            $table->unsignedInteger('approved_by')->nullable()->index();
            $table->index('name');
            $table->index('category_id');
            $table->softDeletes();
        });
        Schema::table('partner_branches', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('academic_requirement');
            $table->dropColumn('english_test_score');
            $table->dropColumn('other_test_score');
            $table->dropSoftDeletes();
            $table->dropColumn('fees');
            $table->dropIndex('name');
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
            $table->dropSoftDeletes();
            $table->dropIndex('name');
            $table->dropIndex('category_id');
            $table->dropIndex('approved_by');
        });
        Schema::table('partner_branches', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
