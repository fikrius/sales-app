@extends('layouts.adminlte')
@section('title','Dashboard')
@section('content')
<div class='row'>
  <div class='col-md-4'><div class='small-box bg-info'><div class='inner'><h3>{{ $totalSales }}</h3><p>Jumlah Transaksi</p></div><div class='icon'><i class='fas fa-shopping-cart'></i></div></div></div>
  <div class='col-md-4'><div class='small-box bg-success'><div class='inner'><h3>Rp {{ number_format($income) }}</h3><p>Total Penjualan</p></div><div class='icon'><i class='fas fa-money-bill'></i></div></div></div>
  <div class='col-md-4'><div class='small-box bg-warning'><div class='inner'><h3>{{ $qty }}</h3><p>Qty Item Terjual</p></div><div class='icon'><i class='fas fa-box'></i></div></div></div>
</div>
<div class='row'>
  <div class='col-md-6'><div class='card'><div class='card-header'>Penjualan per Bulan</div><div class='card-body'><canvas id='salesChart'></canvas></div></div></div>
  <div class='col-md-6'><div class='card'><div class='card-header'>Qty Item Terjual</div><div class='card-body'><canvas id='itemsChart'></canvas></div></div></div>
</div>
@endsection
@push('scripts')
<script>
fetch('/api/charts/sales-monthly').then(r=>r.json()).then(res=>{ new Chart(document.getElementById('salesChart'),{ type:'line', data:{ labels:res.labels, datasets:[{ label:'Sales', data:res.data, tension:0.3 }] } }); });
fetch('/api/charts/items-pie').then(r=>r.json()).then(res=>{ new Chart(document.getElementById('itemsChart'),{ type:'pie', data:{ labels:res.labels, datasets:[{ data:res.data }] } }); });
</script>
@endpush
