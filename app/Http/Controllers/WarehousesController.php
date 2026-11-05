<?php
namespace App\Http\Controllers;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    public function __construct() { $this->middleware('auth'); }
    
    public function index() {
        $warehouses = Warehouse::orderBy('warehouse_name')->paginate(50);
        return view('warehouses.index', compact('warehouses'));
    }
    
    public function create() {
        $code = $this->generateCode();
        return view('warehouses.create', compact('code'));
    }
    
    public function store(Request $request) {
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:150',
            'location' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['warehouse_code'] = $this->generateCode();
        $validated['created_date'] = now();
        Warehouse::create($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse created!');
    }
    
    public function edit($uid) {
        $warehouse = Warehouse::findOrFail($uid);
        return view('warehouses.edit', compact('warehouse'));
    }
    
    public function update(Request $request, $uid) {
        $warehouse = Warehouse::findOrFail($uid);
        $validated = $request->validate([
            'warehouse_code' => 'required|string|max:50|unique:warehouses,warehouse_code,' . $uid . ',uid',
            'warehouse_name' => 'required|string|max:150',
            'location' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $validated['updated_date'] = now();
        $warehouse->update($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated!');
    }
    
    private function generateCode() {
        $last = Warehouse::where('warehouse_code', 'like', 'WH%')->orderBy('warehouse_code', 'desc')->first();
        $num = $last ? ((int)str_replace('WH', '', $last->warehouse_code)) + 1 : 1;
        return 'WH' . str_pad($num, 2, '0', STR_PAD_LEFT);
    }
}
