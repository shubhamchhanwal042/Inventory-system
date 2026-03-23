@extends('layout')

@section('content')

<h3>Create Order</h3>

<input id="customer_name" class="form-control mb-2" placeholder="Customer Name">

<select id="warehouse_id" class="form-control mb-2"></select>

<select id="product_id" class="form-control mb-2"></select>

<input id="qty" class="form-control mb-2" placeholder="Quantity">

<button onclick="createOrder()" class="btn btn-primary mb-4">Create Order</button>

<hr>

<h3>Orders List</h3>

<!-- Filters -->
<div class="mb-2 d-flex gap-2">
    <select id="status_filter" class="form-control w-auto">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <input type="date" id="from_date" class="form-control w-auto" placeholder="From">
    <input type="date" id="to_date" class="form-control w-auto" placeholder="To">
    <button class="btn btn-secondary" onclick="applyFilters()">Filter</button>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order Id</th>
            <th>Customer</th>
            <th>WareHouse</th>
            <th>Status</th>
            <th>Total</th>
            <th>Items</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="orderTable"></tbody>
</table>

<!-- Pagination -->
<div id="pagination" class="mt-2"></div>

<script>
const token = localStorage.getItem('token');

// LOAD PRODUCTS
async function loadProducts(){
    let res = await fetch('/api/products',{
        headers:{'Authorization':'Bearer '+token}
    });
    let products = await res.json();
    let html='<option value="">Select Product</option>';
    products?.forEach(p=>{
        html += `<option value="${p.id}">${p.name}</option>`;
    });
    document.getElementById('product_id').innerHTML = html;
}

// LOAD WAREHOUSES
async function loadWarehouses(){
    let res = await fetch('/api/warehouses',{
        headers:{'Authorization':'Bearer '+token}
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
        alert(data.message);
        return;
    }

    alert('Order created successfully!');
    loadOrders();
}

// APPLY FILTERS
function applyFilters(){
    const status = document.getElementById('status_filter').value;
    const from = document.getElementById('from_date').value;
    const to = document.getElementById('to_date').value;
    loadOrders(status, from, to, 1);
}

// LOAD ORDERS
async function loadOrders(status='', start_date='', end_date='', page=1) {
    let url = `/api/orders?status=${status}&start_date=${start_date}&end_date=${end_date}&page=${page}`;
    let res = await fetch(url,{
        headers:{'Authorization':'Bearer '+token}
    });

    let orders = await res.json();
    let list = orders.data ?? orders;
    let html='';

    list.forEach(order=>{
        console.log(order)
        let itemsList = order.items.map(i => `${i.product_name} x ${i.qty}`).join(', ');
        let confirmDisabled = order.status !== 'Pending' ? 'disabled' : '';
        let cancelDisabled = order.status === 'Cancelled' ? 'disabled' : '';

        html += `
        <tr>
            <td>${order.order_no}</td>
            <td>${order.customer}</td>
            <td>${order.warehouse}</td>
            <td>${order.status}</td>
            <td>${order.total}</td>
            <td>${itemsList}</td>
            <td>
                <button id="confirm-btn-${order.id}" onclick="confirmOrder(${order.id})" class="btn btn-success btn-sm" ${confirmDisabled}>Confirm</button>
                <button id="cancel-btn-${order.id}" onclick="cancelOrder(${order.id})" class="btn btn-danger btn-sm" ${cancelDisabled}>Cancel</button>
            </td>
        </tr>
        `;
    });

    document.getElementById('orderTable').innerHTML = html;

    // Pagination
    let paginationHtml = '';
    for(let i=1; i<=orders.last_page; i++){
        paginationHtml += `<button onclick="loadOrders('${status}','${start_date}','${end_date}',${i})" class="btn btn-sm btn-light m-1">${i}</button>`;
    }
    document.getElementById('pagination').innerHTML = paginationHtml;
}

// CONFIRM ORDER
async function confirmOrder(id) {
    const btn = document.getElementById(`confirm-btn-${id}`);
    btn.disabled = true;          // disable the button
    const originalText = btn.innerText;
    btn.innerText = 'Confirming...'; // show loader text

    try {
        let res = await fetch('/api/orders/' + id + '/confirm', {
            method: 'POST',
            headers: {'Authorization': 'Bearer ' + token}
        });
        let data = await res.json();

        if (!res.ok || !data.success) {
            alert(data.message || "Error confirming order");
        } else {
            alert(data.message);
        }
    } catch(err) {
        alert("Network error: " + err.message);
    } finally {
        btn.disabled = false;       // re-enable
        btn.innerText = originalText;
        loadOrders();               // refresh table to show new status
    }
}

// CANCEL ORDER
async function cancelOrder(id) {
    if (!confirm("Are you sure you want to cancel this order?")) return;

    const btn = document.getElementById(`cancel-btn-${id}`);
    btn.disabled = true;
    const originalText = btn.innerText;
    btn.innerText = 'Cancelling...';

    try {
        let res = await fetch('/api/orders/' + id + '/cancel', {
            method: 'POST',
            headers: {'Authorization': 'Bearer ' + token}
        });
        let data = await res.json();

        if (!res.ok || !data.success) {
            alert(data.message || "Error cancelling order");
        } else {
            alert(data.message);
        }
    } catch(err) {
        alert("Network error: " + err.message);
    } finally {
        btn.disabled = false;
        btn.innerText = originalText;
        loadOrders();
    }
}

// INITIAL LOAD
loadProducts();
loadWarehouses();
loadOrders();

</script>

@endsection