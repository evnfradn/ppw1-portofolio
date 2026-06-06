<?php
include_once("config.php");
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int) $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");

if (mysqli_num_rows($query) == 0) {
    header('Location: index.php');
    exit();
}

$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa - SIM Mahasiswa</title>
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
            --warning: #f59e0b;
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

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover { background-color: #d97706; }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
        }

        /* MAIN */
        main {
            flex: 1;
            padding: 2rem;
            max-width: 700px;
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
            padding: 2rem 1.5rem;
        }

        /* FOTO */
        .foto-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }

        .foto-besar {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--border);
        }

        .foto-placeholder {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            font-weight: 700;
        }

        .nama-besar {
            margin-top: 1rem;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .jurusan-badge {
            margin-top: 0.4rem;
            display: inline-block;
            background-color: #ede9fe;
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* INFO TABLE */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .info-table tr {
            border-bottom: 1px solid var(--border);
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table td {
            padding: 0.875rem 0.5rem;
            vertical-align: top;
        }

        .info-table td:first-child {
            color: var(--text-muted);
            width: 160px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-table td:last-child {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* AKSI */
        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 2rem;
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
                <h2 class="card-title">Detail Mahasiswa</h2>
                <a href="index.php" class="btn btn-secondary btn-sm">
                    <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                    Kembali
                </a>
            </div>

            <div class="card-body">

                <!-- Foto & nama -->
                <div class="foto-section">
                    <?php if (!empty($data['foto']) && file_exists("uploads/mahasiswa/" . $data['foto'])): ?>
                        <img src="uploads/mahasiswa/<?= htmlspecialchars($data['foto']) ?>" alt="Foto" class="foto-besar">
                    <?php else: ?>
                        <div class="foto-placeholder">
                            <?= strtoupper(explode(' ', $data['nama'])[0][0] ?? 'M') ?>
                        </div>
                    <?php endif; ?>
                    <div class="nama-besar"><?= htmlspecialchars($data['nama']) ?></div>
                    <span class="jurusan-badge"><?= htmlspecialchars($data['jurusan']) ?></span>
                </div>

                <!-- Info detail -->
                <table class="info-table">
                    <tr>
                        <td>
                            <i data-lucide="hash" style="width: 1rem; height: 1rem;"></i>
                            NIM
                        </td>
                        <td><?= htmlspecialchars($data['nim']) ?></td>
                    </tr>
                    <tr>
                        <td>
                            <i data-lucide="mail" style="width: 1rem; height: 1rem;"></i>
                            Email
                        </td>
                        <td><?= htmlspecialchars($data['email']) ?></td>
                    </tr>
                    <tr>
                        <td>
                            <i data-lucide="map-pin" style="width: 1rem; height: 1rem;"></i>
                            Alamat
                        </td>
                        <td><?= !empty($data['alamat']) ? htmlspecialchars($data['alamat']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td>
                            <i data-lucide="calendar" style="width: 1rem; height: 1rem;"></i>
                            Tanggal Daftar
                        </td>
                        <td><?= date('d F Y', strtotime($data['created_at'])) ?></td>
                    </tr>
                </table>

                <!-- Tombol aksi -->
                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">
                        <i data-lucide="arrow-left" style="width: 1rem; height: 1rem;"></i>
                        Kembali
                    </a>
                    <a href="edit.php?id=<?= $data['id'] ?>" class="btn btn-warning">
                        <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i>
                        Edit Data
                    </a>
                </div>

            </div>
        </div>
    </main>

    <footer class="app-footer">
        <p>&copy; <?= date("Y") ?> SIM Mahasiswa &bull; Praktikum Web Programming &bull; All Rights Reserved.</p>
    </footer>

</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>