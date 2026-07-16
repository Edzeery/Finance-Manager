<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('super-admin.account.profile');
    }
}
