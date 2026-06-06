<?php
include_once("config.php");
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success = '';
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($username))
        $errors[] = 'Username tidak boleh kosong';
    if (empty($email))
        $errors[] = 'Email tidak boleh kosong';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Format email tidak valid';
    if (empty($full_name))
        $errors[] = 'Nama lengkap tidak boleh kosong';
    if (strlen($password) < 6)
        $errors[] = 'Password minimal 6 karakter';
    if ($password !== $confirm)
        $errors[] = 'Konfirmasi password tidak cocok';

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0)
        $errors[] = 'Username atau email sudah terdaftar';

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, full_name, password) VALUES ('$username', '$email', '$full_name', '$hashed')";

        if (mysqli_query($conn, $sql)) {
            $success = 'Registrasi berhasil! Silakan login.';
        } else {
            $errors[] = 'Error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SIM Mahasiswa</title>
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

        .register-box {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
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

        .error ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .success {
            background-color: #dcfce7;
            color: #15803d;
            padding: 0.6rem 0.8rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #555;
        }

        .login-link a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Daftar Akun</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <?= htmlspecialchars($success) ?>
            <br><a href="login.php">Login sekarang</a>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="full_name" required placeholder="Nama lengkap kamu"
                value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Username kamu"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="email@domain.com"
                value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" required placeholder="Ketik ulang password">
        </div>
        <button type="submit" name="register">Daftar Sekarang</button>
    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Login disini</a>
    </div>
</div>

</body>
</html>