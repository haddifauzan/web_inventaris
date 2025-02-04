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
                'url' => route('dashboard.index'),
            ],
        ];
        return view('admin.index', compact('title', 'breadcrumbs'));
    }
}
