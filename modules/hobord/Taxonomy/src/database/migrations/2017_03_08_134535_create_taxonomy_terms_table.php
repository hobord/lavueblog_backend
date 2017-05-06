<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxonomyTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('taxonomy_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
        Schema::table('taxonomy_terms', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('taxonomy_terms')->onDelete('cascade');
            $table->foreign('taxonomy_id')->references('id')->on('taxonomies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
