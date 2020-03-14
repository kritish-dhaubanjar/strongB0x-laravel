<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->integer('bill_id');
            $table->integer('item_id')->nullable();
            $table->string('name');
            $table->double('quantity', 15, 2);
            $table->double('price', 15, 2);
            $table->double('total', 15, 2);
            $table->integer('tax_id')->nullable();
            $table->float('tax', 15, 2)->default('0.00');
            $table->softDeletes();
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
        Schema::dropIfExists('bill_items');
    }
}
