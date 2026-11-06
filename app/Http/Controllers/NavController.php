<?php

namespace App\Http\Controllers;

use App\Services\NavigationCatalog;
use Illuminate\Http\Request;

class NavController extends Controller
{
    public function select(Request $request, string $key)
    {
        $area = NavigationCatalog::findArea($key);
        session([NavigationCatalog::SessionKey => $area['key']]);

        $first = collect($area['links'])->first(function ($link) {
            return empty($link['children']) && !empty($link['route']);
        });
        if ($first && !empty($first['route'])) {
            return redirect()->route($first['route']);
        }

        // All Modules with no direct first route -> modules page or dashboard
        if ($area['key'] === 'AllModules' && \Illuminate\Support\Facades\Route::has('modules.index')) {
            return redirect()->route('modules.index');
        }

        return redirect()->route('dashboard');
    }

    public function clear()
    {
        session([NavigationCatalog::SessionKey => 'Dashboard']);
        return redirect()->route('dashboard');
    }
}
