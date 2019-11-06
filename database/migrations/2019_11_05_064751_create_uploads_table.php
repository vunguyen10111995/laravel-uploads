<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('hashed_name')->unique();
            $table->unsignedInteger('owner_id')->index()->nullable();
            $table->unsignedInteger('destination_type')->nullable();
            $table->unsignedInteger('destination_id')->nullable();
            $table->string('type');
            $table->string('size');
            $table->string('name');
            $table->string('mime')->nullable();
            $table->string('extension');
            $table->string('path');
            $table->timestamps();

            $table->index(['destination_type', 'destination_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uploads');
    }
}
