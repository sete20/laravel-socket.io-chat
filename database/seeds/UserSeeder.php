<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name' => $i,
                'email' => $i . '@email.com',
                'password' => bcrypt(123456789)
            ]);
        }
    }
}
