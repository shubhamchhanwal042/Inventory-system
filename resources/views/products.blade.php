@extends('layout')

@section('content')

<h3>Products</h3>

<table class="table" id="productTable">

</table>

<button onclick="window.location='/add-product'" class="btn btn-secondary">Add Products</button>
<script>

async function loadProducts(){

let token = localStorage.getItem('token');

let res = await fetch('/api/products',{
headers:{
'Authorization':'Bearer '+token
}
});

let products = await res.json();

let html='';

products.forEach(p=>{
html+=`<tr>
<td>${p.name}</td>
<td>${p.category}</td>
<td>${p.base_price}</td>
</tr>`;
});

document.getElementById('productTable').innerHTML=html;

}

loadProducts();

</script>

@endsection