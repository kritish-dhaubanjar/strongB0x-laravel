<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']);
            $table->year('paid_year');
            $table->enum('paid_month', ['01','02','03','04','05','06','07','08','09','10','11','12']);
            $table->enum('paid_day', ['01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32']);
            $table->double('amount', 15, 2);
            $table->integer('account_id');
            $table->integer('document_id')->nullable();
            $table->integer('contact_id')->nullable();
            $table->integer('category_id')->default(1);
            $table->text('description')->nullable();
            $table->enum('payment_method', ['Cash', 'Bank Transfer', 'Cheque']);
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
        Schema::dropIfExists('transactions');
    }
}
