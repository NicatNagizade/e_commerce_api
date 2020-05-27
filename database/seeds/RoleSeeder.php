<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['system', 'admin','moderator','helper'];
        foreach($names as $name){
            $roleObj = new Role;
            $roleObj->name = $name;
            $roleObj->save();
        }
    }
}
