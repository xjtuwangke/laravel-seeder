<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-8-7
 * Time: 19:13
 */

namespace Xjtuwangke\LaravelSeeder;
use Keboola\Csv\CsvFile;

class BasicTableSeeder extends \Seeder {

    protected $tables = [ 'someModel' ];

    protected $doNotDelete = [];

    /**
     * @var null faker
     */
    protected $faker = null;

    public function run(){

        \Eloquent::unguard();

        $tables = [];

        foreach( $this->tables as $model ){
            $tables[ $model::getTableName() ] = $model;
        }
        $this->tables = $tables;

        $tables = array_reverse( $this->tables );

        foreach( $tables as $table => $model ){
            if( ! in_array( $model , $this->doNotDelete ) ){
                \DB::table( $table )->truncate();
            }
        }

        $this->faker = KFaker::factory();
        foreach( $this->tables as $table => $model ){
            $this->oneTable( $table , $model );
        }
    }

    protected function oneTable( $table , $model ){

        $this->command->info( "starting to seed table:{$table} with model:{$model}" );

        $method = 'seeds_' . strtolower( $table ) ;
        $model_namespace = explode( '\\' , $model );
        $method2 = 'seeds_model_' . array_pop( $model_namespace );
        if( method_exists( $this ,  $method ) ){
            $this->seed( $table , $model , $method );
        }
        elseif( method_exists( $this ,  $method2 ) ){
            $this->seed( $table , $model , $method2 );
        }
        else{
            $this->command->error( "method not found for table {$table}" );
        }
    }

    protected function seed( $table , $model , $method ){
        $data = $this->$method();
        if( null === $data ){
            $this->command->info( "table {$table} skipped" );
            return;
        }
        $count = count( $data );
        $i = 1;
        foreach( $data as $row ){
            $this->command->line( "seeding table {$table} {$i}/{$count}" );
            $model::create( $row );
            $i++;
        }
        $this->command->info( "table {$table} finished" );
    }

    protected function seeds_(){
        return [];
    }

    protected function readCSV( $csv ){
        $csvFile = new CsvFile( app_path( "database/csv/{$csv}" ) );
        $data = [];
        foreach( $csvFile as $row ){
            $data[] = $row;
        }
        unset( $csvFile );
        $attributes = array_shift( $data );
        $seeds = [];
        foreach( $data as $row ){
            $seed = [];
            foreach( $row as $key => $val ){
                $seed[ $attributes[$key] ] = $val;
            }
            $seeds[] = $seed;
        }
        return $seeds;
    }

    protected function fake_html5(){
        $image = $this->faker->imageUrl( 600 , 400 , 'business') . rand( 0 , 10 );
        $html5 = <<<HTML
<h1>{$this->faker->sentence}</h1>
<h3 style="text-align:right">{$this->faker->sentence}</h3>
<p>{$this->faker->paragraph}</p>
<ul>
<li>{$this->faker->paragraph}</li>
<li>{$this->faker->paragraph}</li>
</ul>
<p>{$this->faker->paragraph}</p>
<img src="{$image}">
<p>{$this->faker->paragraph}</p>
HTML;
        return $html5;
    }
}