<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 10px;">

        <h3 class="text-center mb-4">Login</h3>

        <form id="loginForm">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" id="email" class="form-control" placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Enter password">
            </div>

            <div class="d-grid">
                <button class="btn btn-primary">Login</button>
            </div>
                
                 <div class="d-grid">
                    <p>Don't have an account? <a href="/registerForm">Register here</a></p>
                </div>

        </form>

    </div>

</div>

<script>

document.getElementById('loginForm').addEventListener('submit',async function(e){

e.preventDefault();

let response = await fetch('/api/login',{
method:'POST',
headers:{
'Content-Type':'application/json'
},
body:JSON.stringify({
email:document.getElementById('email').value,
password:document.getElementById('password').value
})
});

let data = await response.json();

localStorage.setItem('token',data.token);

window.location='/dashboard';

});

</script>

</body>
</html>