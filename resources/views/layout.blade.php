<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand">Inventory System</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Common for both roles -->
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link">Dashboard</a>
                </li>

                <!-- Admin Only -->
                <li class="nav-item admin-link">
                    <a href="/add-product" class="nav-link">Products</a>
                </li>
                <li class="nav-item admin-link">
                    <a href="/warehouses" class="nav-link">Warehouses</a>
                </li>

                <!-- Both Admin & Manager -->
                <li class="nav-item">
                    <a href="/orders" class="nav-link">Orders</a>
                </li>
                <li class="nav-item">
                    <a href="/reports" class="nav-link">Reports</a>
                </li>
            </ul>

            <!-- Logout Button -->
            <button onclick="logout()" class="btn btn-danger btn-sm">
                Logout
            </button>
        </div>
    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');

    if(!token) return;

    // Fetch logged-in user
    const res = await fetch('/api/check', {
        headers: { 'Authorization': 'Bearer ' + token }
    });

    const user = await res.json();

    // Hide admin links if role is manager
    if(user.role === 'manager'){
        document.querySelectorAll('.admin-link').forEach(el => el.style.display = 'none');
    }
});

// Logout function
async function logout(){
    const token = localStorage.getItem('token');

    await fetch('/api/logout',{
        method:'POST',
        headers:{ 'Authorization':'Bearer '+token }
    });

    localStorage.removeItem('token');
    window.location="/login";
}
</script>

</body>
</html>