<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Province;
use Illuminate\Console\Command;

class Address extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(3600);
        $requestProvince = 'http://geoapi.heartrails.com/api/json?method=getPrefectures';
        $responseProvince  = file_get_contents($requestProvince);
        $provinces = json_decode($responseProvince, true);

        foreach($provinces['response']['prefecture'] as $value){
            $province['name'] = $value;
            $dtCity['province_id'] = Province::insertGetId($province);
//            var_dump()
            $requestCity = 'http://geoapi.heartrails.com/api/json?method=getCities&prefecture='. urlencode($value);
            $responseCity = file_get_contents($requestCity);
            $cities = json_decode($responseCity, true);

            foreach($cities['response']['location'] as $val){
                $dtCity['name'] = $val['city'];
                City::insert($dtCity);
            }
        }
    }
}
