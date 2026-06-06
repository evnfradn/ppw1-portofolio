<?php
include_once("config.php");
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            header('Location: index.php');
            exit();
        }
    }
    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Data Mahasiswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 380px;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            color: #555;
        }

        input {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        button {
            width: 100%;
            padding: 0.7rem;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        button:hover {
            background-color: #4338ca;
        }

        .error {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.6rem 0.8rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #555;
        }

        .register-link a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Data Mahasiswa</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label>Username atau Email</label>
            <input type="text" name="username" required placeholder="Masukkan username atau email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" name="login">Masuk</button>
    </form>

    <div class="register-link">
        Belum punya akun? <a href="register.php">Daftar disini</a>
    </div>
</div>

</body>
</html>