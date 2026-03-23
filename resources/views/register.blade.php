<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 10px;">

        <h3 class="text-center mb-4">Register</h3>

        <form id="registerForm">

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" id="name" class="form-control" placeholder="Enter your name">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" id="email" class="form-control" placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Enter password">
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select id="role" class="form-select">
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                </select>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary">Register</button>
            </div>

            <div class="d-grid">
                <p>Already have an account? <a href="/login">Login here</a></p>
                
            </div>

        </form>

    </div>

</div>

<script>

document.getElementById('registerForm').addEventListener('submit',async function(e){

e.preventDefault();

let response = await fetch('/api/register',{
method:'POST',
headers:{
'Content-Type':'application/json'
},
body:JSON.stringify({
name:document.getElementById('name').value,
email:document.getElementById('email').value,
password:document.getElementById('password').value,
role:document.getElementById('role').value
})
});

let data = await response.json();

alert("User Registered");

window.location="/login";

});

</script>

</body>
</html>