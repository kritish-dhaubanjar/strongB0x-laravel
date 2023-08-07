<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role_id');
            $table->boolean('enabled')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(['name'=>'John Doe', 'email'=>'johndoe@example.org', 'phone'=>'+977 98XXXXXXXX', 'password'=>bcrypt('abcd1234'), 'role_id'=>1 ,'enabled'=>1]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
