<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tax_number')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        DB::table('companies')->insert(['name'=>'Gimmick Box Engineering Solutions Pvt. Ltd.','tax_number'=>'609571792', 'email'=>'info@gimmickbox.com.np', 'phone_number'=>'+977 9843684612', 'address'=>'Pulchwok-3, Lalitpur']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
