<?php
session_start(); // Mulai session untuk menyimpan data antar request

// Inisialisasi array tugas jika belum tersedia
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Proses data jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tambah tugas baru
    if (isset($_POST['add']) && !empty(trim($_POST['task']))) {
        $_SESSION['tasks'][] = [
            'text' => htmlspecialchars(trim($_POST['task'])), // Simpan teks tugas
            'done' => false // Status awal belum selesai
        ];
    }

    // Toggle status tugas (selesai ↔ belum selesai)
    if (isset($_POST['toggle']) && is_numeric($_POST['toggle'])) {
        $index = (int) $_POST['toggle'];
        if (isset($_SESSION['tasks'][$index])) {
            $_SESSION['tasks'][$index]['done'] = !$_SESSION['tasks'][$index]['done'];
        }
    }

    // Hapus tugas berdasarkan index
    if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
        $index = (int) $_POST['delete'];
        if (isset($_SESSION['tasks'][$index])) {
            array_splice($_SESSION['tasks'], $index, 1);
        }
    }

    // Redirect agar mencegah pengiriman ulang form saat refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ToDo List - PHP</title>
    <!-- Load Bootstrap 5 dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="fw-bold">ToDo List</h1>
        </div>

        <!-- Form tambah tugas -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form class="d-flex gap-2" method="POST" action="">
                    <input type="text" name="task" class="form-control" placeholder="Tugas baru..." required>
                    <button type="submit" name="add" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>

        <!-- Daftar tugas -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Daftar Tugas</h5>

                <?php if (empty($_SESSION['tasks'])): ?>
                    <p class="text-muted">Belum ada tugas.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($_SESSION['tasks'] as $i => $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <!-- Form checkbox toggle -->
                                <form method="POST" class="d-flex align-items-center w-100" action="">
                                    <input type="hidden" name="toggle" value="<?= $i ?>">
                                    <input type="checkbox" class="form-check-input me-3" onchange="this.form.submit()" <?= $task['done'] ? 'checked' : '' ?>>
                                    <span class="flex-grow-1 <?= $task['done'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                        <?= $task['text'] ?>
                                    </span>
                                </form>

                                <!-- Tombol hapus -->
                                <form method="POST" action="" onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                                    <input type="hidden" name="delete" value="<?= $i ?>">
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
