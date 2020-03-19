<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('rate', 15, 2);
            $table->boolean('enabled')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('taxes')->insert(['name'=>'VAT (13%)', 'rate'=>'13.00']);
        DB::table('taxes')->insert(['name'=>'Sales Tax (9%)', 'rate'=>'9.00']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
}
