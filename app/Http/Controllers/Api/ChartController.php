<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;

class ChartController extends Controller
{
    public function salesMonthly(Request $r)
    {
        $year = $r->input('year', date('Y'));
        $rows = Sale::selectRaw('MONTH(date) as month, SUM(total_price) as total')->whereYear('date', $year)->groupBy('month')->orderBy('month')->get();
        $data = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $data[(int)$row->month] = (int)$row->total;
        }
        return response()->json(['labels' => array_map(function ($m) {
            return date('F', mktime(0, 0, 0, $m, 1));
        }, array_keys($data)), 'data' => array_values($data)]);
    }
    
    public function itemsPie(Request $r)
    {
        $rows = SaleItem::selectRaw('item_id, SUM(qty) as qty_sum')->groupBy('item_id')->with('item')->get();
        $labels = [];
        $data = [];
        foreach ($rows as $row) {
            $labels[] = $row->item->name ?? 'Unknown';
            $data[] = (int)$row->qty_sum;
        }
        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
