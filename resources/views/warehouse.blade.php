@extends('layout')

@section('content')

<div class="container mt-5">

    <h2 class="text-center mb-4">Warehouse Management</h2>

    <!-- Warehouse Creation Form -->
    <div class="card shadow-lg p-4 mb-4">
        <h4 class="card-title text-center mb-4">Create Warehouse</h4>
        <form id="createWarehouseForm">
            <div class="mb-3">
                <label for="name" class="form-label">Warehouse Name</label>
                <input type="text" id="name" class="form-control" placeholder="Enter warehouse name" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" class="form-control" placeholder="Enter location" required>
            </div>

            <div class="d-grid">
                <button type="button" class="btn btn-primary" onclick="createWarehouse()">Create</button>
            </div>
        </form>
    </div>

    <!-- Warehouse Table -->
    <div class="card shadow-lg p-4">
        <h4 class="card-title text-center mb-4">Warehouse List</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="warehouseTable"></tbody>
        </table>
    </div>

  

</div>

<script>

const token = localStorage.getItem('token');

// Load all warehouses
loadWarehouses();

async function loadWarehouses(){
    const res = await fetch('/api/warehouses', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    });

    const data = await res.json();

    let html = '';
    data.data.forEach(w => {
        html += `
        <tr>
            <td>${w.id}</td>
            <td>${w.name}</td>
            <td>${w.location}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="deleteWarehouse(${w.id})">Delete</button>
            </td>
        </tr>
        `;
    });

    document.getElementById('warehouseTable').innerHTML = html;
}

// Create new warehouse
async function createWarehouse() {
    const token = localStorage.getItem('token');

    let name = document.getElementById('name').value;
    let location = document.getElementById('location').value;

    // 1️⃣ Call API to create warehouse
    let res = await fetch('/api/warehouses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
        },
        body: JSON.stringify({
            name: name,
            location: location
        })
    });

    let data = await res.json();

    // 2️⃣ Handle success or error
    if (!res.ok || !data.success) {
        alert(data.message || "Error creating warehouse");
        return;
    }

    // ✅ Show success message
    alert(data.message || "Warehouse created successfully!");

    // 3️⃣ Refresh warehouse list
    loadWarehouses();
}

// Delete warehouse
async function deleteWarehouse(id) {
    const token = localStorage.getItem('token');

    // 1️⃣ Confirm before deleting
    if (!confirm("Are you sure you want to delete this warehouse?")) {
        return; // User canceled
    }

    // 2️⃣ Call API to delete
    let res = await fetch('/api/warehouses/' + id, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + token
        }
    });

    let data = await res.json();

    // 3️⃣ Show success or error message
    if (!res.ok) {
        alert(data.message || "Error deleting warehouse");
        return;
    }

    alert(data.message || "Warehouse deleted successfully!");

    // 4️⃣ Refresh warehouse list
    loadWarehouses();
}

// Logout functionality
function logout(){
    localStorage.removeItem('token');
    window.location.href = "/login";
}

</script>

@endsection