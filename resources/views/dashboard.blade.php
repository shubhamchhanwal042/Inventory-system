

@extends('layout')

@section('content')
<style>
body{font-family:Arial; padding:30px; background:#f4f6f9;}
.container{display:flex; justify-content:space-around;}
.card{
  background:white; padding:20px; border-radius:8px; width:200px; text-align:center;
  box-shadow:0 0 5px rgba(0,0,0,0.2);
}
h1{margin:0; font-size:40px;}
h3{margin-top:10px; font-weight:normal;}
button{padding:10px 15px; margin-top:20px; cursor:pointer;}
</style>
</head>
<body>


<div class="container">
  <div class="card">
    <h1 id="productCount">0</h1>
    <h3>Products</h3>
  </div>
  <div class="card">
    <h1 id="warehouseCount">0</h1>
    <h3>Warehouses</h3>
  </div>
  <div class="card">
    <h1 id="totalStock">0</h1>
    <h3>Total Stock</h3>
  </div>
</div>


<script>
const token = localStorage.getItem('token');

async function loadDashboard(){
    let res = await fetch('/api/dashboard',{
        headers:{'Authorization':'Bearer '+token}
    });
    let data = await res.json();
    document.getElementById('productCount').innerText = data.products;
    document.getElementById('warehouseCount').innerText = data.warehouses;
    document.getElementById('totalStock').innerText = data.total_stock;
}

function logout(){
    localStorage.removeItem('token');
    window.location.href="/login";
}

loadDashboard();
</script>
@endsection
