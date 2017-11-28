<?php

namespace App\Console\Commands;


use App\Models\User;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use DateTime;

class TimeDeleteImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TimeDeleteImage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Image Time';

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
        set_time_limit(300);

        //$numberDayDeleteImage = Setting::mediaidRead('MediaidSettingCompany.numberDayDeleteImage',false);

        $cpnName= Company::lists('name','id'); 
        foreach ($cpnName as $key => $value) {
            $this->deleteImage($key);
        }
     

    }

    private function deleteImage($idCpn=null){
        if(!empty($idCpn)){
            $numberDayDeleteImage = Setting::mediaidRead('MediaidSettingCompany.numberDayDeleteImage',false);
            //dd($numberDayDeleteImage);
            $imageCreated = Order::select('created_at')
                                    ->where('orders.id','=',$idCpn)
                                    ->get()->first();
                                    //dd($imageCreated);

            $datetime1 = new DateTime(date('Y-m-d',time()));
            $datetime2 = new DateTime(date('Y-m-d',strtotime($imageCreated['created_at'])));
            $interval = $datetime2->diff($datetime1)->days;
            //dd($interval);
            $settingDeleteImage = $interval - (int)$numberDayDeleteImage;
            //dd($settingDeleteImage);
        }
    }
}