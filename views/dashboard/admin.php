<?php
/* =============================================
   SMPM — View: Dashboard Admin
   Variabel dari DashboardController::dataAdmin():
   $user, $users, $kelompokList, $uploads,
   $tugasList, $penilaianList, $totalMhs, $totalDosen
   ============================================= */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">

    <div class="page-header">
      <h1>Selamat Datang, <?= htmlspecialchars($user['nama']) ?> 👋</h1>
      <p>Panel Administrator — Kelola seluruh data sistem.</p>
    </div>

    <!-- Stats -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Mahasiswa</div>
        <div class="stat-value"><?= $totalMhs ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Dosen</div>
        <div class="stat-value"><?= $totalDosen ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Kelompok</div>
        <div class="stat-value"><?= count($kelompokList) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Upload</div>
        <div class="stat-value"><?= count($uploads) ?></div>
      </div>
    </div>

    <!-- User terdaftar -->
    <div class="card mt-16">
      <div class="flex justify-between items-center mb-16" style="flex-wrap:wrap;gap:8px">
        <div class="card-title" style="margin:0">User Terdaftar</div>
        <a href="index.php?page=manageUser" class="btn btn-primary btn-sm">Kelola User →</a>
      </div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Nama</th><th>Email</th><th>Role</th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td>
                <div class="flex items-center gap-8">
                  <div class="avatar" style="width:28px;height:28px;font-size:.7rem">
                    <?= htmlspecialchars($u['avatar'] ?? '--') ?>
                  </div>
                  <?= htmlspecialchars($u['nama']) ?>
                </div>
              </td>
              <td class="text-muted text-sm"><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php $bc = match($u['role']) {
                    'admin'     => 'badge-red',
                    'dosen'     => 'badge-amber',
                    'mahasiswa' => 'badge-blue',
                    default     => 'badge-navy',
                }; ?>
                <span class="badge <?= $bc ?>"><?= ucfirst($u['role']) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Kelompok -->
    <div class="card mt-16">
      <div class="flex justify-between items-center mb-16" style="flex-wrap:wrap;gap:8px">
        <div class="card-title" style="margin:0">Semua Kelompok</div>
        <a href="index.php?page=manageKelompok" class="btn btn-primary btn-sm">Kelola Kelompok →</a>
      </div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Kelompok</th><th>Tema</th><th>Dosen</th><th>Progress</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($kelompokList as $k): ?>
            <tr>
              <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
              <td class="text-muted text-sm"><?= htmlspecialchars($k['tema']) ?></td>
              <td class="text-sm"><?= htmlspecialchars($k['dosen_nama'] ?? '—') ?></td>
              <td style="min-width:140px">
                <div class="flex items-center gap-8">
                  <div class="progress-wrap" style="flex:1">
                    <?php $pc = $k['progress'] >= 70 ? 'progress-green' : ($k['progress'] >= 40 ? 'progress-blue' : 'progress-amber'); ?>
                    <div class="progress-fill <?= $pc ?>" style="width:<?= $k['progress'] ?>%"></div>
                  </div>
                  <span class="text-sm font-600"><?= $k['progress'] ?>%</span>
                </div>
              </td>
              <td>
                <span class="badge <?= $k['status'] === 'aktif' ? 'badge-green' : 'badge-navy' ?>">
                  <?= ucfirst($k['status']) ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
