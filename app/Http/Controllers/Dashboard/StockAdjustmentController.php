<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product', 'user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $adjustments = $query->latest()->paginate(10)->withQueryString();

        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::all();
        return view('adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            if ($request->type === 'subtraction' && $product->stock < $request->quantity) {
                return back()->withInput()->with('error', 'Cannot subtract more than current system stock.');
            }

            // Adjust stock
            if ($request->type === 'addition') {
                $product->increment('stock', $request->quantity);
            } else {
                $product->decrement('stock', $request->quantity);
            }

            // Create Adjustment Log
            StockAdjustment::create([
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'adjusted_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('adjustments.index')->with('success', 'Stock adjustment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adjusting stock: ' . $e->getMessage());
        }
    }
}
