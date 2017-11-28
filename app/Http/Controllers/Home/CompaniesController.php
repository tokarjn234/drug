<?php


namespace App\Http\Controllers\Home;



class CompaniesController extends HomeAppController
{
	/**
     * Show Company
     * @return View/show
     */

    public function show($id){

        return view('home.companies.show');
    }

    /**
     * Show Company
     * @return View/edit
     */

    public function edit($id) {
        return view('home.companies.edit');
    }

    /**
     * Show Company
     * @return View/update
     */

    public function update($request) {
        return redirect('home.companies')->with('success', 'Save Successfully');
    }
}