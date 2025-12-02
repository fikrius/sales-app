<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Payment;

class DataTableController extends Controller
{
    public function sales(Request $r)
    {
        $query = Sale::query();

        // Search filter
        if ($r->input('search.value')) {
            $s = $r->input('search.value');
            $query->where('code', 'like', '%' . $s . '%');
        }

        // Date range filter
        if ($r->start_date) {
            $query->whereDate('date', '>=', $r->start_date);
        }
        if ($r->end_date) {
            $query->whereDate('date', '<=', $r->end_date);
        }

        // Status filter
        if ($r->status) {
            $query->where('status', $r->status);
        }

        $total = $query->count();
        $start = (int)$r->input('start', 0);
        $length = (int)$r->input('length', 10);
        $data = $query->orderBy('id', 'desc')->skip($start)->take($length)->get();
        $rows = [];
        foreach ($data as $d) {
            // Action buttons
            $editBtn = '';
            $deleteBtn = '';
            $user = Auth::user();

            // Edit button - only for unpaid sales with update permission
            if ($d->status == 'Belum Dibayar' && $user && $user->can('sale-update')) {
                $editBtn = '<a class="btn btn-sm btn-warning" href="/sales/' . $d->id . '/edit"><i class="fas fa-edit"></i></a> ';
            }

            // Delete button - only for unpaid sales with delete permission
            if ($d->status == 'Belum Dibayar' && $user && $user->can('sale-delete')) {
                $deleteBtn = '<form action="/sales/' . $d->id . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>';
            }

            // Status badge
            $statusBadge = '';
            if ($d->status == 'Sudah Dibayar' || $d->status == 'Lunas') {
                $statusBadge = '<span class="badge badge-success">' . $d->status . '</span>';
            } elseif ($d->status == 'Belum Dibayar Sepenuhnya') {
                $statusBadge = '<span class="badge badge-info">' . $d->status . '</span>';
            } else {
                $statusBadge = '<span class="badge badge-warning">' . $d->status . '</span>';
            }

            $rows[] = [
                $d->code,
                $d->date,
                'Rp ' . number_format($d->total_price, 0, ',', '.'),
                $statusBadge,
                '<a class="btn btn-sm btn-primary" href="/sales/' . $d->id . '"><i class="fas fa-eye"></i></a> ' . $editBtn . $deleteBtn
            ];
        }
        return response()->json(['draw' => (int)$r->input('draw', 1), 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $rows]);
    }
    public function items(Request $r)
    {
        $query = Item::query();

        if ($r->input('search.value')) {
            $s = $r->input('search.value');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', '%' . $s . '%')
                    ->orWhere('code', 'like', '%' . $s . '%');
            });
        }

        $total = $query->count();
        $start = (int)$r->input('start', 0);
        $length = (int)$r->input('length', 10);
        $data = $query->orderBy('id', 'desc')->skip($start)->take($length)->get();
        $rows = [];

        foreach ($data as $d) {
            // Image thumbnail
            $imageHtml = '<span class="badge badge-secondary">No Image</span>';
            if ($d->image) {
                $imageUrl = asset('storage/' . $d->image);
                $imageHtml = '<img src="' . $imageUrl . '" alt="' . $d->name . '" style="width: 50px; height: 50px; object-fit: cover;" class="img-thumbnail">';
            }

            // Action buttons
            $user = Auth::user();
            $viewBtn = '';
            $editBtn = '';
            $deleteBtn = '';

            // View button for admin and superadmin with permission
            if ($user && $user->can('item-read')) {
                $viewBtn = '<a class="btn btn-sm btn-primary" href="/items/' . $d->id . '"><i class="fas fa-eye"></i></a> ';
            }

            // Edit button for admin and superadmin with permission
            if ($user && $user->can('item-update')) {
                $editBtn = '<a class="btn btn-sm btn-warning" href="/items/' . $d->id . '/edit"><i class="fas fa-edit"></i></a> ';
            }

            // Delete button only for superadmin with permission
            if ($user && $user->can('item-delete')) {
                $deleteBtn = '<form action="/items/' . $d->id . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>';
            }

            $rows[] = [
                $imageHtml,
                $d->code,
                $d->name,
                'Rp ' . number_format($d->price, 0, ',', '.'),
                $viewBtn . $editBtn . $deleteBtn
            ];
        }

        return response()->json([
            'draw' => (int)$r->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $rows
        ]);
    }
    public function payments(Request $r)
    {
        $query = Payment::with('sale');

        // Date filter
        if ($r->start_date) {
            $query->whereDate('date', '>=', $r->start_date);
        }
        if ($r->end_date) {
            $query->whereDate('date', '<=', $r->end_date);
        }

        // Status filter
        if ($r->status) {
            $query->whereHas('sale', function ($q) use ($r) {
                $q->where('status', $r->status);
            });
        }

        // Search filter
        if ($r->input('search.value')) {
            $s = $r->input('search.value');
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', '%' . $s . '%')
                    ->orWhereHas('sale', function ($sq) use ($s) {
                        $sq->where('code', 'like', '%' . $s . '%');
                    });
            });
        }

        $total = $query->count();
        $start = (int)$r->input('start', 0);
        $length = (int)$r->input('length', 10);
        $data = $query->orderBy('id', 'desc')->skip($start)->take($length)->get();
        $rows = [];

        foreach ($data as $d) {
            // Action buttons
            $user = Auth::user();
            $viewBtn = '<a class="btn btn-sm btn-primary" href="/payments/' . $d->id . '"><i class="fas fa-eye"></i></a> ';
            $editBtn = '';
            $deleteBtn = '';

            // Edit button for admin and superadmin with permission
            if ($user && $user->can('payment-update')) {
                $editBtn = '<a class="btn btn-sm btn-warning" href="/payments/' . $d->id . '/edit"><i class="fas fa-edit"></i></a> ';
            }

            // Delete button only for superadmin with permission
            if ($user && $user->can('payment-delete')) {
                $deleteBtn = '<form action="/payments/' . $d->id . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>';
            }

            // Status badge for sale
            $statusBadge = '';
            if ($d->sale) {
                if ($d->sale->status == 'Sudah Dibayar' || $d->sale->status == 'Lunas') {
                    $statusBadge = '<span class="badge badge-success">' . $d->sale->status . '</span>';
                } elseif ($d->sale->status == 'Belum Dibayar Sepenuhnya') {
                    $statusBadge = '<span class="badge badge-info">' . $d->sale->status . '</span>';
                } else {
                    $statusBadge = '<span class="badge badge-warning">' . $d->sale->status . '</span>';
                }
            }

            $rows[] = [
                $d->code,
                $d->sale->code ?? '',
                'Rp ' . number_format($d->amount, 0, ',', '.'),
                \App\Helpers\Helper::formatDate($d->date),
                $statusBadge,
                $viewBtn . $editBtn . $deleteBtn
            ];
        }

        return response()->json([
            'draw' => (int)$r->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $rows
        ]);
    }

    public function searchItems(Request $r)
    {
        $search = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = Item::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $total = $query->count();
        $items = $query->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $results = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->code . ' - ' . $item->name . ' (' . \App\Helpers\Helper::formatRupiah($item->price) . ')',
                'price' => $item->price
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
}
