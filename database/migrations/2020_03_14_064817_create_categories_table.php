<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'other', 'item']);
            $table->boolean('enabled')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('categories')->insert(['name'=>'Deposit', 'type'=>'income']);
        DB::table('categories')->insert(['name'=>'Sales', 'type'=>'income']);
        DB::table('categories')->insert(['name'=>'Other', 'type'=>'expense']);
        DB::table('categories')->insert(['name'=>'General', 'type'=>'item']);
        DB::table('categories')->insert(['name'=>'Transfer', 'type'=>'other']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
