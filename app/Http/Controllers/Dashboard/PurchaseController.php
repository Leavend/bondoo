<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Helpers\IdGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('purchase_no', 'like', '%' . $search . '%')
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
        }

        $purchases = $query->latest()->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'purchase_status' => 'required|in:pending,ordered,received',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit_type' => 'required|in:primary,secondary',
            'pay_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchase_no = IdGenerator::generate([
                'table' => 'purchases',
                'field' => 'purchase_no',
                'length' => 10,
                'prefix' => 'PO-',
                'reset_on_prefix_change' => true
            ]);

            // Calculate Subtotal
            $sub_total = 0;
            $details = [];

            foreach ($request->items as $item) {
                $line_total = $item['quantity'] * $item['unit_cost'];
                $sub_total += $line_total;

                $details[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'unit_type' => $item['unit_type'],
                    'total' => $line_total,
                ];
            }

            $tax = 0; // standard zero tax for simplified PO
            $total = $sub_total + $tax;
            $pay_amount = $request->pay_amount;
            if ($pay_amount > $total) {
                $pay_amount = $total;
            }
            $due_amount = $total - $pay_amount;

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'purchase_no' => $purchase_no,
                'purchase_date' => Carbon::parse($request->purchase_date),
                'purchase_status' => $request->purchase_status,
                'sub_total' => $sub_total,
                'tax' => $tax,
                'total' => $total,
                'pay_amount' => $pay_amount,
                'due_amount' => $due_amount,
            ]);

            foreach ($details as $detail) {
                PurchaseDetails::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_type' => $detail['unit_type'],
                    'unit_cost' => $detail['unit_cost'],
                    'total' => $detail['total'],
                ]);

                // If status is received, add to product stock
                if ($request->purchase_status === 'received') {
                    $product = Product::find($detail['product_id']);
                    // Multiply quantity if purchased as secondary unit (box)
                    $multiplier = ($detail['unit_type'] === 'secondary') ? $product->conversion_rate : 1;
                    $product->increment('stock', $detail['quantity'] * $multiplier);
                }
            }

            DB::commit();

            return redirect()->route('purchases.index')->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating Purchase: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'details.product'])->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:received',
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::with('details.product')->findOrFail($id);

            if ($purchase->purchase_status === 'received') {
                return back()->with('error', 'Purchase is already received.');
            }

            $purchase->purchase_status = 'received';
            $purchase->save();

            // Increment product stock, converting secondary units (e.g. box) to primary
            foreach ($purchase->details as $detail) {
                $product = $detail->product;
                $multiplier = ($detail->unit_type === 'secondary' && $product) ? $product->conversion_rate : 1;
                $product->increment('stock', $detail->quantity * $multiplier);
            }

            DB::commit();

            return back()->with('success', 'Purchase status updated to Received. Stocks updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function payDue(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:purchases,id',
            'pay_amount' => 'required|numeric|min:1',
        ]);

        $purchase = Purchase::findOrFail($request->id);

        if ($request->pay_amount > $purchase->due_amount) {
            return back()->with('error', 'Pay amount exceeds the remaining debt.');
        }

        $purchase->pay_amount += $request->pay_amount;
        $purchase->due_amount -= $request->pay_amount;
        $purchase->save();

        return back()->with('success', 'Debt paid successfully.');
    }
}
