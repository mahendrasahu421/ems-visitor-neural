<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin - all menus
        DB::table('role_menu')->insert(
            DB::table('menus')->selectRaw('1 as role_id, id as menu_id, 1 as can_view, 1 as can_edit, NOW() as created_at, NOW() as updated_at')->get()->toArray()
        );

        // HR - limited menus
        DB::table('role_menu')->insert([
            ['role_id'=>2,'menu_id'=>1,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>2,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>3,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>4,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>5,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>6,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>7,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>8,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>9,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>10,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>11,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>2,'menu_id'=>12,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Employee - only ESS menus
        DB::table('role_menu')->insert([
            ['role_id'=>3,'menu_id'=>1,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>58,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>59,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>60,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>61,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>62,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
            ['role_id'=>3,'menu_id'=>63,'can_view'=>1,'can_edit'=>0,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
