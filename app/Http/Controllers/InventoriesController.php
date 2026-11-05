<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inventories = Inventory::with('product')->paginate(50);
        return view('inventories.index', compact('inventories'));
    }
}
