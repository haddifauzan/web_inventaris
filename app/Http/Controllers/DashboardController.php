<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index()
    {
        $title = 'Dashboard';
        $breadcrumbs = [
            [
                'text' => 'Dashboard',
                'url' => auth()->user()->role === 'admin' ? 
                    route('admin.dashboard') : 
                    route('user.dashboard'),
            ],
        ];
        if (auth()->user()->role === 'admin') {
            return view('admin.index', compact('title', 'breadcrumbs'));
        } else {
            return view('user.index', compact('title', 'breadcrumbs'));
        }
    }
}
