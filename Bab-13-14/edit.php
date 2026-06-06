<?php
include_once("config.php");
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}
$id = (int) $_GET["id"];

$result = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");
if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}
$row = mysqli_fetch_assoc($result);
$current_foto = $row["foto"];

$errors = [];

if (isset($_POST['update'])) {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $foto_filename = $current_foto;

    if (empty($nim)) {
        $errors[] = 'NIM tidak boleh kosong';
    } elseif (!preg_match('/^[0-9]+$/', $nim)) {
        $errors[] = 'NIM hanya boleh berisi angka, tidak boleh mengandung huruf atau karakter lain';
    } elseif (strlen($nim) < 8 || strlen($nim) > 12) {
        $errors[] = 'Panjang NIM harus antara 8 hingga 12 digit (saat ini: ' . strlen($nim) . ' karakter)';
    }
    if (empty($nama)) $errors[] = 'Nama tidak boleh kosong';
    if (empty($jurusan)) $errors[] = 'Jurusan tidak boleh kosong';
    if (empty($email)) {
        $errors[] = 'Email tidak boleh kosong';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }

    if (empty($errors)) {
        $chk = mysqli_query($conn, "SELECT id FROM mahasiswa WHERE nim='$nim' AND id != $id");
        if (mysqli_num_rows($chk) > 0) $errors[] = 'NIM sudah terdaftar untuk mahasiswa lain';
    }

    $hapus_foto = isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1';
    if ($hapus_foto) {
        if ($current_foto) deleteFile($current_foto);
        $foto_filename = null;
    }

    if (empty($errors) && !empty($_FILES['foto']['name'])) {
        $upload = uploadFile($_FILES['foto']);
        if ($upload['success']) {
            if ($current_foto && !$hapus_foto) deleteFile($current_foto);
            $foto_filename = $upload['filename'];
        } else {
            $errors[] = $upload['message'];
        }
    }

    if (empty($errors)) {
        $foto_sql = $foto_filename ? "'$foto_filename'" : 'NULL';
        $sql = "UPDATE mahasiswa SET nim='$nim', nama='$nama', jurusan='$jurusan', email='$email', alamat='$alamat', foto=$foto_sql WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit();
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
    <title>Edit Mahasiswa - SIM Mahasiswa</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --danger: #ef4444;
            --text-primary: #1e1b4b;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --bg: #f0f2f5;
            --border: #e5e7eb;
        }

        body {
            font-family: sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* NAV */
        .app-nav {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .app-brand-icon {
            background-color: var(--primary);
            color: white;
            width: 2rem;
            height: 2rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* BUTTON */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: background 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover { background-color: var(--primary-dark); }

        .btn-secondary {
            background-color: white;
            color: var(--text-secondary);
            border-color: var(--border);
        }

        .btn-secondary:hover { background-color: #f9fafb; }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
        }

        /* MAIN */
        main {
            flex: 1;
            padding: 2rem;
            max-width: 860px;
            width: 100%;
            margin: 0 auto;
        }

        /* CARD */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ALERT */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .alert ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        /* GRID */
        .grid-2 {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 2rem;
        }

        /* FOTO KOLOM */
        .foto-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 0.5rem;
            gap: 0.75rem;
        }

        .avatar-large {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--border);
        }

        .avatar-placeholder {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .file-upload-box {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            width: 100%;
            cursor: pointer;
            position: relative;
        }

        .file-upload-box input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-upload-box:hover {
            border-color: var(--primary);
            background-color: #f5f3ff;
        }

        .upload-text {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.4rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--danger);
            cursor: pointer;
        }

        /* FORM */
        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
            font-family: sans-serif;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 90px;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        /* FOOTER */
        .app-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            background: white;
        }

        /* ANIMATION */
        .animate-fade-in {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="app-container">

    <nav class="app-nav animate-fade-in">
        <a href="index.php" class="app-brand">
            <div class="app-brand-icon">
                <i data-lucide="graduation-cap"></i>
            </div>
            <span>Data Mahasiswa</span>
        </a>
        <div class="user-profile-nav">
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                <div class="user-role">Administrator</div>
            </div>
            <a href="logout.php" class="btn btn-secondary btn-sm">
                <i data-lucide="log-out" style="width: 1rem; height: 1rem;"></i>
                Keluar
            </a>
        </div>
    </nav>

    <main class="animate-fade-in">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Edit Data Mahasiswa</h2>
                <a href="index.php" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                    Kembali
                </a>
            </div>

            <div class="card-body">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i data-lucide="alert-circle" style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;"></i>
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <div class="grid-2">

                        <!-- Kolom kiri: foto -->
                        <div class="foto-col">
                            <?php if (!empty($current_foto) && file_exists("uploads/mahasiswa/" . $current_foto)): ?>
                                <img id="avatar-preview" class="avatar-large"
                                     src="uploads/mahasiswa/<?= htmlspecialchars($current_foto) ?>"
                                     alt="Foto Mahasiswa">
                                <div id="avatar-placeholder" class="avatar-placeholder" style="display:none;">
                                    <?= strtoupper(explode(' ', $row['nama'])[0][0] ?? 'M') ?>
                                </div>
                            <?php else: ?>
                                <img id="avatar-preview" class="avatar-large" src="" alt="" style="display:none;">
                                <div id="avatar-placeholder" class="avatar-placeholder">
                                    <?= strtoupper(explode(' ', $row['nama'])[0][0] ?? 'M') ?>
                                </div>
                            <?php endif; ?>

                            <div class="file-upload-box">
                                <i data-lucide="upload-cloud" style="width: 1.5rem; height: 1.5rem; color: var(--text-muted);"></i>
                                <div class="upload-text">Pilih foto baru<br>JPG, PNG, GIF - Max 5MB</div>
                                <input type="file" name="foto" id="foto" accept="image/*" onchange="previewImage(event)">
                            </div>

                            <?php if (!empty($current_foto) && file_exists("uploads/mahasiswa/" . $current_foto)): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="hapus_foto" value="1" id="hapus_foto" onchange="toggleHapusFoto(this)">
                                    Hapus foto saat ini
                                </label>
                            <?php endif; ?>
                        </div>

                        <!-- Kolom kanan: data -->
                        <div>
                            <div class="form-group">
                                <label class="form-label">NIM <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="nim" class="form-control" required placeholder="8-12 digit angka"
                                    value="<?= htmlspecialchars(isset($_POST['nim']) ? $_POST['nim'] : $row['nim']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap mahasiswa"
                                    value="<?= htmlspecialchars(isset($_POST['nama']) ? $_POST['nama'] : $row['nama']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jurusan <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="jurusan" class="form-control" required placeholder="Contoh: Teknik Informatika"
                                    value="<?= htmlspecialchars(isset($_POST['jurusan']) ? $_POST['jurusan'] : $row['jurusan']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email <span style="color:var(--danger);">*</span></label>
                                <input type="email" name="email" class="form-control" required placeholder="email@domain.com"
                                    value="<?= htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : $row['email']) ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" placeholder="Alamat lengkap (opsional)"><?= htmlspecialchars(isset($_POST['alamat']) ? $_POST['alamat'] : $row['alamat']) ?></textarea>
                            </div>

                            <div class="form-actions">
                                <a href="index.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i data-lucide="save" style="width: 1rem; height: 1rem;"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <p>&copy; <?= date("Y") ?> SIM Mahasiswa &bull; Praktikum Web Programming &bull; All Rights Reserved.</p>
    </footer>

</div>

<script>
    lucide.createIcons();

    function previewImage(event) {
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        const chkHapus = document.getElementById('hapus_foto');
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                if (chkHapus) chkHapus.checked = false;
            }
            reader.readAsDataURL(file);
        }
    }

    function toggleHapusFoto(checkbox) {
        const preview = document.getElementById('avatar-preview');
        const fileInput = document.getElementById('foto');
        if (checkbox.checked) {
            preview.style.opacity = '0.3';
            fileInput.value = '';
        } else {
            preview.style.opacity = '1';
        }
    }
</script>
</body>
</html>