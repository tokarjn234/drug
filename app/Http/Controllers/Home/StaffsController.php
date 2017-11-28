<?php

namespace App\Http\Controllers\Home;


use App\Models\Staff;
use Illuminate\Http\Request;
use DB;
use Hash;

class StaffsController extends HomeAppController
{
    /**
     * Staffs list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getIndex(Request $request) {

        $staffQuery = Staff::select('staffs.*');

        $paginate = $staffQuery->orderBy('staffs.id', 'desc')->paginate(5);
        $staffs = Staff::render($paginate);

        return view('home.staffs.index', ['paginate' => $paginate, 'staffs' => $staffs]);
    }


    /**
     * Staffs list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    

    public function postIndex(Request $request) {
    
    	//$staffQuery = Staff::select('staffs.*');
    
    	//$paginate = $staffQuery->orderBy('staffs.id', 'desc')->paginate(5);
    	//$staffs = Staff::render($paginate);
    
    	return view('home.staffs.index', ['paginate' => $paginate, 'staffs' => $staffs]);
    }
}