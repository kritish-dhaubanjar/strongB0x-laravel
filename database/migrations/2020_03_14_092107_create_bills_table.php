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
            // $table->dateTime('billed_at');
            // $table->dateTime('due_at')->nullable();
            $table->year('billed_year');
            $table->enum('billed_month', ['01','02','03','04','05','06','07','08','09','10','11','12']);
            $table->enum('billed_day', ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32']);
            $table->year('due_year')->nullable();
            $table->enum('due_month', ['01','02','03','04','05','06','07','08','09','10','11','12'])->nullable();
            $table->enum('due_day', ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32'])->nullable();
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
