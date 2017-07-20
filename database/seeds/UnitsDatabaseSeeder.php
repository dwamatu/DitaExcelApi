<?php

use Illuminate\Database\Seeder;

class UnitsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table('units')->insert([
            'name' => $faker->randomElement($array =
                    array("ACS-", "MIS-", "COM-", "ART-", "ENG-", "HPE-", "BIL-", "MAT-", "PHY-", "PHL-"))
                .$faker->randomElement(
                        $array =array("111", "113", "112", "114", "115", "116", "211", "212", "213", "300")),
            'room' => str_random(1) . mt_rand(1, 20),
            'date' => $faker->date(),
            'shift' => $faker->randomElement($array = array('Day', 'Evening', 'Athi')),
            'section' => $faker->randomElement($array = array('A', 'B', 'C')),
        ]);

    }
}
