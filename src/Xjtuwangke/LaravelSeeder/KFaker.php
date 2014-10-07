<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-7
 * Time: 22:39
 */

namespace Xjtuwangke\LaravelSeeder;

class KFaker {

    static function factory(){

        $faker = new \Faker\Generator();
        $faker->addProvider( new \Faker\Provider\zh_CN\Person( $faker ) );
        $faker->addProvider( new \Faker\Provider\zh_CN\Address($faker) );
        $faker->addProvider( new \Faker\Provider\zh_CN\PhoneNumber($faker) );
        $faker->addProvider( new \Faker\Provider\zh_CN\Company($faker) );
        $faker->addProvider( new \Faker\Provider\Lorem($faker) );
        $faker->addProvider( new \Faker\Provider\DateTime( $faker ) );
        $faker->addProvider( new \Faker\Provider\Internet( $faker ) );

        $faker->seed( rand( 0 , 65535 ) );

        return $faker;
    }

    static function fakerList( $name , $count = 100 , $unique = true , $faker = null  ){
        if( null == $faker ){
            $faker = static::factory();
        }
        $results = [];
        if( $unique ){
            try{
                for( $i = 0 ; $i < $count ; $i++ ){
                    $results[] = $faker->unique()->$name;
                }
            }catch (\OverflowException $e) {
                die( "ERROR: faker could not generate $count unique $name(s)" );
            }
        }
        else{
            for( $i = 0 ; $i < $count ; $i++ ){
                $results[] = $faker->$name;
            }
        }
        return $results;
    }
}