<?php

namespace App\Http\Controllers;

use App\Services\NavigationCatalog;
use App\Services\RoleModuleAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ModulesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sections = [];
        foreach (NavigationCatalog::moduleSections() as $section) {
            $modules = array_values(array_filter($section['modules'], function ($m) use ($user) {
                if (empty($m['route']) || !Route::has($m['route'])) {
                    return false;
                }
                return RoleModuleAccess::canAccess($m['controller'] ?? null, $user);
            }));
            if ($modules) {
                $section['modules'] = $modules;
                $sections[] = $section;
            }
        }
        return view('modules.index', compact('sections'));
    }
}
