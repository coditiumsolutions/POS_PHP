<?php

namespace App\Http\Controllers;

use App\Services\NavigationCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NavController extends Controller
{
    public function select(Request $request, string $key)
    {
        $area = NavigationCatalog::findArea($key);
        session([NavigationCatalog::SessionKey => $area['key']]);

        // Optional deep-link: /nav/select/AllModules?to=vendors.index
        $to = $request->query('to');
        if (is_string($to) && Route::has($to)) {
            return redirect()->route($to);
        }

        // All Modules hub page
        if ($area['key'] === 'AllModules' && Route::has('modules.index')) {
            return redirect()->route('modules.index');
        }

        $first = collect($area['links'])->first(function ($link) {
            return empty($link['children']) && !empty($link['route']);
        });
        if ($first && !empty($first['route'])) {
            return redirect()->route($first['route']);
        }

        return redirect()->route('dashboard');
    }

    public function clear()
    {
        session([NavigationCatalog::SessionKey => 'Dashboard']);
        return redirect()->route('dashboard');
    }
}
