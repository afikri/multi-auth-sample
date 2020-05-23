<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        DB::table('users')->insert([
			[
			'name'=>'alex',
            'email'=>'alex@jung.de',
            'is_admin' => '1',
			'password'=>bcrypt('alex@jung.de'),
            'remember_token'=> str_random(25),
            'created_at'=> date(now()),
            'updated_at'=>date(now())
            ],
            [
                'name'=>'chloe',
                'email'=> 'chloe@gmx.de',
                'is_admin' => '0',
                'password'=>bcrypt('chloe@gmx.de'),
                'remember_token'=> str_random(25),
                'created_at'=>date(now()),
                'updated_at'=>date(now())
            ]
		
		]);
    }
}
