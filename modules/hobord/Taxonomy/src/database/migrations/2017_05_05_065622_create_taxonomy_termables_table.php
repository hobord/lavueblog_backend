<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxonomyTermablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy_termables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('taxonomy_term_id');
            $table->unsignedInteger('taxonomy_termable_id');
            $table->string('taxonomy_termable_type');
            $table->timestamps();
        });
        Schema::table('taxonomy_termable', function (Blueprint $table) {
            $table->unique(['taxonomy_term_id', 'taxonomy_termable_type', 'taxonomy_termable_id'], 'termable_unique');
            $table->foreign('taxonomy_term_id')->references('id')->on('taxonomy_terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
