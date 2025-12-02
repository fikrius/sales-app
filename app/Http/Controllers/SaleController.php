<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $recentSales = Sale::with('items.item')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        return view('sales.index', compact('recentSales'));
    }

    public function create()
    {
        // Items will be loaded via AJAX for better performance
        return view('sales.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ], [
            'items.required' => 'Minimal 1 item harus dipilih',
            'items.*.item_id.required' => 'Item harus dipilih',
            'items.*.item_id.exists' => 'Item tidak valid',
            'items.*.qty.required' => 'Qty harus diisi',
            'items.*.qty.min' => 'Qty minimal 1',
            'items.*.price.required' => 'Harga harus diisi'
        ]);

        $maxAttempts = 5;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                return DB::transaction(function () use ($r) {
                    // Generate code inside transaction with lock
                    $code = \App\Helpers\Helper::generateCode('SL', Sale::class);

                    $sale = Sale::create([
                        'code' => $code,
                        'date' => now(),
                        'status' => 'Belum Dibayar'
                    ]);

                    $total = 0;
                    foreach ($r->items as $it) {
                        $line = (int)$it['qty'] * (int)$it['price'];
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'item_id' => $it['item_id'],
                            'qty' => $it['qty'],
                            'price' => $it['price'],
                            'total' => $line
                        ]);
                        $total += $line;
                    }

                    $sale->update(['total_price' => $total]);
                    return redirect()->route('sales.index')->with('success', 'Sale created');
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // If duplicate key error, retry
                if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                    if ($attempt >= $maxAttempts - 1) {
                        // Last attempt failed, throw error
                        return redirect()->back()->with('error', 'Failed to create sale after multiple attempts. Please try again.');
                    }
                    // Small random delay before retry to reduce collision
                    usleep(mt_rand(50000, 150000)); // 50-150ms random delay
                    continue;
                }
                // Other database errors
                throw $e;
            }
        }

        return redirect()->back()->with('error', 'Failed to create sale. Please try again.');
    }

    public function show(Sale $sale)
    {
        $sale->load('items.item', 'payments');
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->status != 'Belum Dibayar') {
            return redirect()->route('sales.index')->with('error', 'Cannot edit paid sales');
        }

        $sale->load('items.item');
        // Items will be loaded via AJAX for better performance
        return view('sales.edit', compact('sale'));
    }

    public function update(Request $r, Sale $sale)
    {
        if ($sale->status != 'Belum Dibayar') {
            return redirect()->route('sales.index')->with('error', 'Cannot edit paid sales');
        }

        $r->validate(['items' => 'required|array']);

        // Delete old sale items
        $sale->items()->delete();

        $total = 0;
        foreach ($r->items as $it) {
            $line = (int)$it['qty'] * (int)$it['price'];
            SaleItem::create(['sale_id' => $sale->id, 'item_id' => $it['item_id'], 'qty' => $it['qty'], 'price' => $it['price'], 'total' => $line]);
            $total += $line;
        }

        $sale->update(['total_price' => $total]);
        return redirect()->route('sales.show', $sale->id)->with('success', 'Sale updated successfully');
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status != 'Belum Dibayar') {
            return redirect()->route('sales.index')->with('error', 'Cannot delete paid sales. Only unpaid sales can be deleted.');
        }

        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully');
    }
}
