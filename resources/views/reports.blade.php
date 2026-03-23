@extends('layout')

@section('content')

<h2>Reports</h2>

<hr>

<h4>Stock Summary Report</h4>

<table class="table table-bordered">

<thead>
<tr>
<th>Product</th>
<th>Warehouse</th>
<!-- <th>Opening Stock</th> -->
<th>Sold Quantity</th>
<th>Current Stock</th>
<th>Total Sales</th>
</tr>
</thead>
<tbody id="stockReport"></tbody>

</table>

<hr>

<h4>Sales Report (Date Wise)</h4>

<table class="table table-bordered">

<thead>
<tr>
<th>Date</th>
<th>Total Sales</th>
</tr>
</thead>

<tbody id="salesReport"></tbody>

</table>

<hr>

<h4>Top 5 Selling Products</h4>

<table class="table table-bordered">
<thead>
<tr>
<th>Product</th>
<th>Total Sold</th>
<th>Total Revenue</th>
</tr>
</thead>
<tbody id="topProducts"></tbody>

</table>

<script>

const token = localStorage.getItem('token');

async function loadStockReport(){
    let res = await fetch('/api/reports/stock-summary',{
        headers:{'Authorization':'Bearer '+token}
    });

    let data = await res.json();
    let html='';

    data.forEach(row=>{
        html+=`
        <tr>
            <td>${row.product}</td>
            <td>${row.warehouse}</td>
            <td>${row.sold_quantity}</td>
            <td>${row.current_stock}</td>
            <td>${row.total_sales.toLocaleString()}</td>
        </tr>
        `;
    });

    document.getElementById('stockReport').innerHTML=html;
}

async function loadSalesReport(){

let res = await fetch('/api/reports/sales',{
headers:{
'Authorization':'Bearer '+token
}
});

let data = await res.json();

let html='';

data.forEach(row=>{
html+=`
<tr>
<td>${row.date}</td>
<td>${row.total_sales}</td>
</tr>
`;
});

document.getElementById('salesReport').innerHTML=html;

}

async function loadTopProducts(){
    let res = await fetch('/api/reports/top-products',{
        headers:{'Authorization':'Bearer '+token}
    });

    let data = await res.json();
    let html='';

    data.forEach(row=>{
        html+=`
        <tr>
            <td>${row.name}</td>
            <td>${row.total_sold}</td>
            <td>${row.total_revenue.toLocaleString()}</td>
        </tr>
        `;
    });

    document.getElementById('topProducts').innerHTML=html;
}
loadStockReport();
loadSalesReport();
loadTopProducts();

</script>

@endsection