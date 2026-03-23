@extends('layout')

@section('content')
<style>
body{font-family:Arial; padding:30px;}
input,select,button{padding:8px;margin:5px;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ddd;padding:8px;text-align:center;}
button{background:#007bff;color:white;border:none;cursor:pointer;}
.delete{background:red;}
</style>
</head>
<body>

<h2>Add Product + Stock</h2>

<div>
<input type="text" id="product_name" placeholder="Product Name">
<input type="text" id="category" placeholder="Category">
<input type="number" id="base_price" placeholder="Base Price">

<select id="warehouse_id"></select>
<input type="number" id="stock_qty" placeholder="Quantity">

<button onclick="addProductStock()">Add Product + Stock</button>
</div>

<h3>Products + Stock</h3>
<table>
<thead>
<tr>
<th>Product</th>
<th>Warehouse</th>
<th>Stock</th>
</tr>
</thead>
<tbody id="tableBody"></tbody>
</table>

<script>
const token = localStorage.getItem('token');

loadWarehouses();
loadProductsStock();

async function loadWarehouses(){
  let res = await fetch('/api/warehouses',{headers:{'Authorization':'Bearer '+token}});
  let data = await res.json();
  let html = '<option value="">Select Warehouse</option>';
  data.data.forEach(w=> html += `<option value="${w.id}">${w.name}</option>`);
  document.getElementById('warehouse_id').innerHTML = html;
}

async function addProductStock() {
    const token = localStorage.getItem('token');

    let pname = document.getElementById('product_name').value;
    let cat = document.getElementById('category').value;
    let price = document.getElementById('base_price').value;
    let wid = document.getElementById('warehouse_id').value;
    let qty = document.getElementById('stock_qty').value;

    // 1️⃣ Create product
    let res1 = await fetch('/api/products', {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'Authorization':'Bearer ' + token
        },
        body: JSON.stringify({name: pname, category: cat, base_price: price})
    });
    let productData = await res1.json();

    if (!res1.ok || !productData.success) {
        alert(productData.message || "Error creating product");
        return;
    }

    alert(productData.message); // ✅ Shows "Product created successfully!"

    // 2️⃣ Add stock
    let res2 = await fetch('/api/stocks', {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Authorization':'Bearer ' + token
        },
        body: JSON.stringify({
            product_id: productData.data.id,
            warehouse_id: wid,
            quantity: qty
        })
    });

    let stockData = await res2.json();

    if (!res2.ok || !stockData.success) {
        alert(stockData.message || "Error adding stock");
        return;
    }

    alert(stockData.message); // ✅ Shows "Stock added successfully!"

    loadProductsStock(); // Refresh the table/list
}

async function loadProductsStock(){
  let res = await fetch('/api/stocks',{headers:{'Authorization':'Bearer '+token}});
  let data = await res.json();
  let html = '';
  data.data.forEach(s=>{
    html += `<tr>
      <td>${s.product.name}</td>
      <td>${s.warehouse.name}</td>
      <td>${s.quantity}</td>
    </tr>`;
  });
  document.getElementById('tableBody').innerHTML = html;
}
</script>

@endsection