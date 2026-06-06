<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("config.php");
requireLogin();

$limit = 5;
$page = isset($_GET["page"]) ? max(1, (int)$_GET["page"]) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET["search"]) ? mysqli_real_escape_string($conn, $_GET["search"]) : "";
$where = !empty($search) ? "WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' OR jurusan LIKE '%$search%' OR email LIKE '%$search%'" : "";

$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM mahasiswa $where");
$total_data = mysqli_fetch_assoc($count_result)["total"];
$total_pages = ceil($total_data / $limit);
if ($total_pages < 1) $total_pages = 1;

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

$query = "SELECT * FROM mahasiswa $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

$start_entry = $total_data > 0 ? $offset + 1 : 0;
$end_entry = min($offset + $limit, $total_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Mahasiswa</title>
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

        /* NAV */
        .app-nav {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0.875rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
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

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-primary);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* CONTAINER */
        .app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* BUTTONS */
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

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: white;
            color: var(--text-secondary);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-sm {
            padding: 0.35rem 0.7rem;
            font-size: 0.8rem;
        }

        /* ACTION BAR */
        .action-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
        }

        .search-input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 0.55rem 0.8rem 0.55rem 2.4rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.875rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* CARD */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
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
            color: var(--text-primary);
        }

        /* BADGE */
        .badge {
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-info {
            background-color: #ede9fe;
            color: var(--primary);
        }

        /* TABLE */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .table thead th {
            background-color: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #fafafa;
        }

        /* AVATAR */
        .avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            object-fit: cover;
        }

        .actions-cell {
            display: flex;
            gap: 0.4rem;
            align-items: center;
        }

        /* PAGINATION */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 0.3rem;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            color: var(--text-secondary);
            background: white;
            transition: all 0.2s;
        }

        .page-item.active .page-link {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }

        .page-link:hover {
            background-color: #f3f4f6;
        }

        /* FOOTER */
        .app-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            background: white;
            margin-top: auto;
        }

        /* MODAL */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 999;
            justify-content: center;
            align-items: center;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }

        .modal-icon {
            width: 3.5rem;
            height: 3.5rem;
            background-color: #fef3c7;
            color: var(--warning);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .modal-message {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
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
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <a href="logout.php" class="btn btn-secondary btn-sm" title="Keluar">
                    <i data-lucide="log-out" style="width: 1.125rem; height: 1.125rem;"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </nav>

        <main class="animate-fade-in" style="animation-delay: 0.1s;">

            <?php if (isset($_SESSION['message'])): ?>
                <div style="background:#dcfce7;color:#15803d;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.875rem;">
                    <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div style="background:#fee2e2;color:#b91c1c;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.875rem;">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="action-bar">
                <a href="tambah.php" class="btn btn-primary" id="btn-add">
                    <i data-lucide="user-plus" style="width: 1.25rem; height: 1.25rem;"></i>
                    <span>Tambah Mahasiswa</span>
                </a>
                <form action="index.php" method="GET" class="search-form">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-input-icon" style="width: 1.125rem; height: 1.125rem;"></i>
                        <input type="text" name="search" class="form-control" placeholder="Cari NIM, Nama, Jurusan..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn btn-secondary">Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="index.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Mahasiswa</h2>
                    <span class="badge badge-info"><?= $total_data ?> Mahasiswa Terdaftar</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Profil</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Jurusan</th>
                                    <th>Email</th>
                                    <th>Alamat</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($row['foto']) && file_exists("uploads/mahasiswa/" . $row['foto'])): ?>
                                                    <img src="uploads/mahasiswa/<?= htmlspecialchars($row['foto']) ?>" alt="Foto" class="avatar">
                                                <?php else: ?>
                                                    <div class="avatar">
                                                        <?php
                                                        $words = explode(' ', $row['nama']);
                                                        echo strtoupper($words[0][0] ?? 'M');
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($row['nim']) ?></td>
                                            <td style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><span class="badge badge-info"><?= htmlspecialchars($row['jurusan']) ?></span></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <div style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['alamat']) ?>">
                                                    <?= !empty($row['alamat']) ? htmlspecialchars($row['alamat']) : '-' ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-cell">
                                                    <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm" title="Detail" style="color: var(--primary);">
                                                        <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                                    </a>
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm" title="Edit" style="color: var(--warning);">
                                                        <i data-lucide="edit" style="width: 1rem; height: 1rem;"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-secondary btn-sm" title="Hapus" style="color: var(--danger);" onclick="confirmDelete('hapus.php?id=<?= $row['id'] ?>', '<?= htmlspecialchars(addslashes($row['nama'])) ?>')">
                                                        <i data-lucide="trash-2" style="width: 1rem; height: 1rem;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                                            <i data-lucide="users" style="width: 3rem; height: 3rem; opacity: 0.4; display: block; margin: 0 auto 1rem;"></i>
                                            <div style="font-weight: 600;">Tidak Ada Data Mahasiswa</div>
                                            <div style="font-size: 0.85rem; margin-top: 0.3rem;">Silakan tambahkan data baru.</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <div class="pagination-info">
                        Menampilkan <strong><?= $start_entry ?></strong> sampai <strong><?= $end_entry ?></strong> dari <strong><?= $total_data ?></strong> data
                    </div>
                    <ul class="pagination">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <i data-lucide="chevron-left" style="width: 1rem; height: 1rem;"></i>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </main>

        <footer class="app-footer">
            <p>&copy; <?= date("Y") ?> Data Mahasiswa &bull; All Rights Reserved.</p>
        </footer>
    </div>

    <!-- Modal Hapus -->
    <div id="delete-modal" class="modal-backdrop">
        <div class="modal-box">
            <div class="modal-icon">
                <i data-lucide="alert-triangle" style="width: 1.75rem; height: 1.75rem;"></i>
            </div>
            <h3 class="modal-title">Hapus Data</h3>
            <p class="modal-message">Apakah kamu yakin ingin menghapus data mahasiswa <strong id="delete-student-name"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <a id="confirm-delete-btn" href="#" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function confirmDelete(url, name) {
            document.getElementById('delete-student-name').textContent = name;
            document.getElementById('confirm-delete-btn').href = url;
            document.getElementById('delete-modal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('delete-modal').classList.remove('show');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('delete-modal');
            if (event.target == modal) closeModal();
        }
    </script>
</body>
</html>