<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->username = 'system';
        $user->email = 'system@test.com';
        $user->password = Hash::make('password');
        $user->save();
        $user->roles()->attach(1);
        
        $user = new User;
        $user->username = 'admin';
        $user->email = 'admin@test.com';
        $user->password = Hash::make('password');
        $user->save();
        $user->roles()->attach(2);
        
        $user = new User;
        $user->username = 'moderator';
        $user->email = 'moderator@test.com';
        $user->password = Hash::make('password');
        $user->save();
        $user->roles()->attach(3);

        $user = new User;
        $user->username = 'helper';
        $user->email = 'helper@test.com';
        $user->password = Hash::make('password');
        $user->save();
        $user->roles()->attach(4);

        $user = new User;
        $user->username = 'user';
        $user->email = 'user@test.com';
        $user->password = Hash::make('password');
        $user->save();

        $user = new User;
        $user->username = 'user2';
        $user->email = 'user2@test.com';
        $user->password = Hash::make('password');
        $user->save();
    }
}
