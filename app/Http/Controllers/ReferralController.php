<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    //

    public function index()
    {
        return view('referral.index');
    
    }
    public function edit()
    {
        return view('referral.edit');
    
    }
    public function create()
    {
        return view('referral.create');
    
    }
    public function fields()
    {
        return view('referral.fields');
    
    }
    public function show()
    {
        return view('referral.show');
    
    }
    public function table()
    {
        return view('referral.table');
    
    }
    public function data()
    {
        return view('referral.datatables_actions');
    
    }
    public function show_fields()
    {
        return view('referral.show_fields');
    
    }
    
}
