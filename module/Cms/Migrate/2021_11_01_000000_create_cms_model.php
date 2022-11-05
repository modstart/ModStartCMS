<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cms_model', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('title', 50)->nullable()->comment('');
            $table->string('name', 50)->nullable()->comment('');

            $table->string('listTemplate', 100)->nullable()->comment('');
            $table->string('detailTemplate', 100)->nullable()->comment('');

            $table->unique(['name']);

        });

        Schema::create('cms_model_field', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('modelId')->nullable()->comment('');
            $table->integer('sort')->nullable()->comment('');

            $table->string('title', 50)->nullable()->comment('');
            $table->string('name', 50)->nullable()->comment('');

            $table->string('fieldType', 20)->nullable()->comment('');
            $table->text('fieldData')->nullable()->comment('');
            $table->integer('maxLength')->nullable()->comment('');
            $table->tinyInteger('isRequired')->nullable()->comment('');
            $table->tinyInteger('isSearch')->nullable()->comment('');
            $table->tinyInteger('isList')->nullable()->comment('');
            $table->string('placeholder', 100)->nullable()->comment('');

            $table->index(['modelId']);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
