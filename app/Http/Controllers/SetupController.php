<?php

namespace App\Http\Controllers;

use App\Services\NavigationCatalog;
use Illuminate\Support\Facades\Route;

class SetupController extends Controller
{
    public function index()
    {
        $modules = array_values(array_filter(NavigationCatalog::setupModules(), function ($m) {
            return !empty($m['route']) && Route::has($m['route']);
        }));
        return view('setup.index', compact('modules'));
    }
}
