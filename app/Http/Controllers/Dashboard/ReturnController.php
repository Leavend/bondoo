<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReturn::with('product');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('reference_no', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $returns = $query->latest()->paginate(10)->withQueryString();

        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $products = Product::all();
        return view('returns.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,purchase',
            'reference_no' => 'required|string|max:50',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            // Handle Stock Corrections
            if ($request->type === 'sales') {
                // Customer returns item -> Stock goes up
                $product->increment('stock', $request->quantity);
            } else {
                // Return to Supplier -> Stock goes down
                if ($product->stock < $request->quantity) {
                    return back()->withInput()->with('error', 'Cannot return more than current stock count.');
                }
                $product->decrement('stock', $request->quantity);
            }

            // Create Return Log
            ProductReturn::create([
                'type' => $request->type,
                'reference_no' => $request->reference_no,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'refund_amount' => $request->refund_amount,
            ]);

            DB::commit();

            return redirect()->route('returns.index')->with('success', 'Return transaction logged successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error recording return: ' . $e->getMessage());
        }
    }
}
