<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index');
    }

    public function create()
    {
        $sales = Sale::with('payments')
            ->whereIn('status', ['Belum Dibayar', 'Belum Dibayar Sepenuhnya'])
            ->get();
        return view('payments.create', compact('sales'));
    }

    public function store(Request $r)
    {
        $r->validate(['sale_id' => 'required', 'amount' => 'required|numeric|min:1']);
        $sale = Sale::findOrFail($r->sale_id);
        $paidAmount = $sale->payments()->sum('amount');
        $remaining = $sale->total_price - $paidAmount;

        // Validate payment amount
        if ($r->amount > $remaining) {
            return back()->withErrors('Jumlah pembayaran melebihi sisa tagihan. Sisa: ' . \App\Helpers\Helper::formatRupiah($remaining))->withInput();
        }

        if ($r->amount <= 0) {
            return back()->withErrors('Jumlah pembayaran harus lebih dari 0')->withInput();
        }

        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                return DB::transaction(function () use ($r, $sale, $paidAmount) {
                    // Generate code inside transaction
                    $code = \App\Helpers\Helper::generateCode('PY', Payment::class);

                    // Create payment
                    Payment::create([
                        'code' => $code,
                        'sale_id' => $sale->id,
                        'date' => now(),
                        'amount' => $r->amount
                    ]);

                    // Update sale status
                    $newPaidAmount = $paidAmount + $r->amount;
                    if ($newPaidAmount >= $sale->total_price) {
                        $sale->update(['status' => 'Sudah Dibayar']);
                        $message = 'Payment berhasil dibuat. Sale telah LUNAS!';
                    } elseif ($newPaidAmount > 0 && $newPaidAmount < $sale->total_price) {
                        $sale->update(['status' => 'Belum Dibayar Sepenuhnya']);
                        $sisaBayar = $sale->total_price - $newPaidAmount;
                        $message = 'Payment berhasil dibuat. Sisa tagihan: ' . \App\Helpers\Helper::formatRupiah($sisaBayar);
                    } else {
                        $sale->update(['status' => 'Belum Dibayar']);
                        $sisaBayar = $sale->total_price - $newPaidAmount;
                        $message = 'Payment berhasil dibuat. Sisa tagihan: ' . \App\Helpers\Helper::formatRupiah($sisaBayar);
                    }

                    return redirect()->route('payments.index')->with('success', $message);
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // If duplicate key error, retry
                if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                    $attempt++;
                    if ($attempt >= $maxAttempts) {
                        throw $e;
                    }
                    // Small delay before retry
                    usleep(100000); // 100ms
                    continue;
                }
                throw $e;
            }
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('sale.payments');
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load('sale.payments');
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $r, Payment $payment)
    {
        $r->validate([
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date'
        ]);

        $sale = $payment->sale;

        // Calculate total paid excluding current payment
        $otherPaymentsSum = $sale->payments()->where('id', '!=', $payment->id)->sum('amount');

        // Check if new amount would exceed sale total
        if ($otherPaymentsSum + $r->amount > $sale->total_price) {
            return back()->withErrors('Total pembayaran melebihi total sale');
        }

        $payment->update([
            'amount' => $r->amount,
            'date' => $r->date
        ]);

        // Update sale status
        $totalPaid = $sale->payments()->sum('amount');
        if ($totalPaid >= $sale->total_price) {
            $sale->update(['status' => 'Sudah Dibayar']);
        } elseif ($totalPaid > 0 && $totalPaid < $sale->total_price) {
            $sale->update(['status' => 'Belum Dibayar Sepenuhnya']);
        } else {
            $sale->update(['status' => 'Belum Dibayar']);
        }

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
    }

    public function destroy(Payment $payment)
    {
        $sale = $payment->sale;
        
        // Use transaction for data consistency
        DB::transaction(function () use ($payment, $sale) {
            $payment->delete();            // Recalculate sale status after deletion
            $totalPaid = $sale->payments()->sum('amount');

            if ($totalPaid >= $sale->total_price) {
                $sale->update(['status' => 'Sudah Dibayar']);
            } elseif ($totalPaid > 0 && $totalPaid < $sale->total_price) {
                $sale->update(['status' => 'Belum Dibayar Sepenuhnya']);
            } else {
                // No payments left or total paid is 0
                $sale->update(['status' => 'Belum Dibayar']);
            }
        });

        return redirect()->route('payments.index')->with('success', 'Payment berhasil dihapus. Status sale telah diperbarui.');
    }
}
