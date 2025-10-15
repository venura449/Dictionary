<?php
require_once '../../Config/auth.php';
if (auth_is_logged_in()) {
    header('Location: yomupath.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { background: #fff; width: 100%; max-width: 420px; padding: 24px; border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        h1 { margin-bottom: 6px; font-size: 1.6rem; color: #2c3e50; }
        p { margin: 0 0 18px; color: #6b7280; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 6px; }
        .btn { width: 100%; margin-top: 8px; padding: 10px 12px; border: none; border-radius: 6px; background: #3498db; color: #fff; cursor: pointer; font-weight: 600; }
        .btn:disabled { opacity: .6; cursor: not-allowed; }
        .row { display: flex; justify-content: space-between; margin-top: 10px; font-size: .9rem; }
        .error { color: #b91c1c; background: #fee2e2; border: 1px solid #fecaca; padding: 10px; border-radius: 6px; margin-bottom: 10px; display: none; }
        a { color: #2563eb; text-decoration: none; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const err = document.getElementById('errorBox');
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            err.style.display = 'none';
            btn.disabled = true; btn.textContent = 'Signing in...';
            const payload = { email: form.email.value.trim(), password: form.password.value };
            try {
                const res = await fetch('auth_api.php?action=login', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Login failed');
                window.location.href = 'yomupath.php';
            } catch (e) {
                err.textContent = e.message;
                err.style.display = 'block';
            } finally {
                btn.disabled = false; btn.textContent = 'Login';
            }
        });
    });
    </script>
    </head>
<body>
    <div class="card">
        <h1>Sign in</h1>
        <p>Use your email and password.</p>
        <div class="error" id="errorBox"></div>
        <form id="loginForm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required />
            </div>
            <button class="btn" id="loginBtn" type="submit">Login</button>
        </form>
        <div class="row">
            <span>New here?</span>
            <a href="register.php">Create an account</a>
        </div>
    </div>
</body>
</html>


