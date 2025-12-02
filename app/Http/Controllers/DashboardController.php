<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\SaleItem;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = Sale::count();
        $income = Payment::sum('amount');
        $qty = SaleItem::sum('qty');
        return view('dashboard.index', compact('totalSales', 'income', 'qty'));
    }
}
