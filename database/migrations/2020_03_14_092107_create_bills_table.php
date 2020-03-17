<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number');
            $table->string('order_number')->nullable();
            $table->enum('status', ['received', 'partial', 'paid']);
            $table->dateTime('billed_at');
            $table->dateTime('due_at');
            $table->double('amount', 15, 2);
            $table->double('tax_id')->nullable();
            $table->integer('category_id');
            $table->integer('vendor_id');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            // $table->dateTime('deleted_at')->nullable();
            // $table->dateTime('created_at')->nullable();
            // $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
