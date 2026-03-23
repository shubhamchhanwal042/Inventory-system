@extends('layout')

@section('content')



<h3>Create Order</h3>

<input id="customer_name" class="form-control mb-2" placeholder="Customer Name">

<select id="warehouse_id" class="form-control mb-2"></select>

<select id="product_id" class="form-control mb-2"></select>

<input id="qty" class="form-control mb-2" placeholder="Quantity">

<button onclick="createOrder()" class="btn btn-primary">Create Order</button>

<hr>

<h3>Orders List</h3>

<table class="table table-bordered">

<thead>
<tr>
<th>Order Id</th>
<th>Customer</th>
<th>WareHouse</th>
<th>Status</th>
<th>Total</th>
<th>Action</th>
</tr>
</thead>

<tbody id="orderTable"></tbody>

</table>

<script>

const token = localStorage.getItem('token');


// LOAD PRODUCTS
async function loadProducts(){

let res = await fetch('/api/products',{
headers:{
'Authorization':'Bearer '+token
}
});

let products = await res.json();

let html='<option value="">Select Product</option>';
products?.forEach(p=>{
    // console.log(p);

html += `<option value="${p.id}">${p.name}</option>`;
});

document.getElementById('product_id').innerHTML = html;

}


// LOAD WAREHOUSES
async function loadWarehouses(){

let res = await fetch('/api/warehouses',{
headers:{
'Authorization':'Bearer '+token
}
});

let warehouses = await res.json();

let html='<option value="">Select Warehouse</option>';

warehouses.data?.forEach(w=>{
html += `<option value="${w.id}">${w.name}</option>`;
});

document.getElementById('warehouse_id').innerHTML = html;

}


// CREATE ORDER
async function createOrder() {
    const token = localStorage.getItem('token');

    const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            customer_name: document.getElementById('customer_name').value,
            warehouse_id: document.getElementById('warehouse_id').value,
            items: [
                {
                    product_id: document.getElementById('product_id').value,
                    qty: parseInt(document.getElementById('qty').value)
                }
            ]
        })
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
        alert(data.message); // Now shows "Insufficient stock for product: Laptop"
        return;
    }

    alert('Order created successfully!');
}

// LOAD ORDERS
async function loadOrders(){

let res = await fetch('/api/orders',{
headers:{
'Authorization':'Bearer '+token
}
});

let orders = await res.json();

let list = orders.data ?? orders;

let html='';

list.forEach(order=>{

html+=`
<tr>
<td>${order.order_no}</td>
<td>${order.customer}</td>
<td>${order.warehouse}</td>
<td>${order.status}</td>
<td>${order.total}</td>

<td>

<button onclick="confirmOrder(${order.id})" class="btn btn-success btn-sm">
Confirm
</button>

<button onclick="cancelOrder(${order.id})" class="btn btn-danger btn-sm">
Cancel
</button>

</td>

</tr>
`;

});

document.getElementById('orderTable').innerHTML=html;

}


// CONFIRM ORDER
// Confirm order with alert
async function confirmOrder(id) {
    const token = localStorage.getItem('token');

    let res = await fetch('/api/orders/' + id + '/confirm', {
        method: 'POST',
        headers: {'Authorization': 'Bearer ' + token}
    });

    let data = await res.json();

    if (!res.ok || !data.success) {
        alert(data.message || "Error confirming order");
        return;
    }

    alert(data.message); // Order confirmed
    loadOrders();
}

// Cancel order with alert
async function cancelOrder(id) {
    const token = localStorage.getItem('token');

    if (!confirm("Are you sure you want to cancel this order?")) {
        return; // User canceled
    }

    let res = await fetch('/api/orders/' + id + '/cancel', {
        method: 'POST',
        headers: {'Authorization': 'Bearer ' + token}
    });

    let data = await res.json();

    if (!res.ok || !data.success) {
        alert(data.message || "Error cancelling order");
        return;
    }

    alert(data.message); // Order cancelled
    loadOrders();
}


loadProducts();
loadWarehouses();
loadOrders();

</script>

@endsection