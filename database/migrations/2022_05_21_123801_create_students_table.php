<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->decimal('avg1')->nullable();
            $table->decimal('avg2')->nullable();
            $table->decimal('avg3')->nullable();
            $table->decimal('avg4')->nullable();
            $table->decimal('avg5')->nullable();
            $table->decimal('avg6')->nullable();
            $table->decimal('avg7')->nullable();
            $table->decimal('avg8')->nullable();
            $table->decimal('avg9')->nullable();
            $table->decimal('avg10')->nullable();
            $table->boolean('isGrad')->default(false);
            $table->enum('level',['bachaelor','master','pHD']);
            $table->enum('year',['first','second','third','fourth','fifth','sixth','seventh','eigth','ninth','tenth']);
            $table->string('note')->nullable();
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
        Schema::dropIfExists('students');
    }
}
