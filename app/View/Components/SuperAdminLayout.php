<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SuperAdminLayout extends Component
{
    public function render()
    {
        return view('layouts.super-admin.app');
    }
}
