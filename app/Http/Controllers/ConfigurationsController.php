<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Allow admins only - check role_name
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role_name !== 'Administrator') {
                abort(403, 'Only administrators can access system configuration.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $configs = Configuration::orderBy('config_key')->get();
        return view('configurations.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'configs' => 'required|array',
            'configs.*.key' => 'required|string',
            'configs.*.value' => 'nullable|string',
        ]);

        foreach ($validated['configs'] as $config) {
            Configuration::updateOrCreate(
                ['config_key' => $config['key']],
                [
                    'config_value' => $config['value'] ?? '',
                    'updated_date' => now(),
                ]
            );
        }

        return redirect()->route('configurations.index')
            ->with('success', 'Configurations updated successfully!');
    }

    public function create()
    {
        return view('configurations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'config_key' => 'required|string|max:100|unique:configurations',
            'config_value' => 'nullable|string',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_date'] = now();
        Configuration::create($validated);

        return redirect()->route('configurations.index')
            ->with('success', 'Configuration created successfully!');
    }
}
