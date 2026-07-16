<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Livewire\Actions\Logout;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $isSuperAdmin = auth()->user()->hasRole('super_admin');
        $logout = new Logout;
        $logout();
        $request->session()->regenerate();

        return redirect($isSuperAdmin ? route('super.admin.login') : '/');
    }
}
