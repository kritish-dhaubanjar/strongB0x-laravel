<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // $this->call(ItemSeeder::class);
        // $this->call(ContactSeeder::class);
        $this->call(BillSeeder::class);
        $this->call(InvoiceSeeder::class);
        $this->call(TransactionSeeder::class);
    }
}
