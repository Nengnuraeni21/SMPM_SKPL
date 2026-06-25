<?php
/* =============================================
   SMPM — View: Dashboard Dosen
   Variabel dari DashboardController::dataDosen():
   $user, $kelompokList, $uploads, $tugasList
   ============================================= */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">

    <div class="page-header">
      <h1>Selamat Datang, <?= htmlspecialchars($user['nama']) ?> 👋</h1>
      <p>Pantau perkembangan semua kelompok bimbingan Anda.</p>
    </div>

    <!-- Stats -->
    <?php
    $onTrack   = count(array_filter($kelompokList, fn($k) => $k['progress'] >= 50));
    $perhatian = count($kelompokList) - $onTrack;
    ?>
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Total Kelompok</div>
        <div class="stat-value"><?= count($kelompokList) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">On Track</div>
        <div class="stat-value" style="color:var(--success)"><?= $onTrack ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Perlu Perhatian</div>
        <div class="stat-value" style="color:var(--danger)"><?= $perhatian ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Upload</div>
        <div class="stat-value"><?= count($uploads) ?></div>
      </div>
    </div>

    <!-- Status semua kelompok -->
    <div class="card mt-16">
      <div class="flex justify-between items-center mb-16" style="flex-wrap:wrap;gap:8px">
        <div class="card-title" style="margin:0">Status Semua Kelompok</div>
        <a href="index.php?page=tugasDosen" class="btn btn-primary btn-sm">
          + Tambah Tugas
        </a>
      </div>
      <?php if (empty($kelompokList)): ?>
      <p class="text-muted text-sm">Belum ada kelompok.</p>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Kelompok</th><th>Tema Proyek</th><th>Progress</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($kelompokList as $k): ?>
            <tr>
              <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
              <td class="text-muted text-sm"><?= htmlspecialchars($k['tema']) ?></td>
              <td style="min-width:160px">
                <div class="flex items-center gap-8">
                  <div class="progress-wrap" style="flex:1">
                    <?php $pc = $k['progress'] >= 70 ? 'progress-green' : ($k['progress'] >= 40 ? 'progress-blue' : 'progress-amber'); ?>
                    <div class="progress-fill <?= $pc ?>" style="width:<?= $k['progress'] ?>%"></div>
                  </div>
                  <span class="text-sm font-600"><?= $k['progress'] ?>%</span>
                </div>
              </td>
              <td>
                <span class="badge <?= $k['progress'] >= 50 ? 'badge-green' : 'badge-red' ?>">
                  <?= $k['progress'] >= 50 ? 'On Track' : 'Perlu Perhatian' ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- Tugas terbaru -->
    <?php if (!empty($tugasList)): ?>
    <div class="card mt-16">
      <div class="flex justify-between items-center mb-16">
        <div class="card-title" style="margin:0">Tugas Terbaru</div>
        <a href="index.php?page=tugasDosen" class="btn btn-outline btn-sm">Lihat Semua →</a>
      </div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Judul</th><th>Kelompok</th><th>Deadline</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($tugasList, 0, 5) as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['judul']) ?></td>
              <td class="text-sm"><?= htmlspecialchars($t['kelompok_nama'] ?? '—') ?></td>
              <td class="text-sm"><?= date('d M Y', strtotime($t['deadline'])) ?></td>
              <td>
                <?php $bc = match($t['status']) {
                    'selesai'   => 'badge-green',
                    'proses'    => 'badge-blue',
                    'terlambat' => 'badge-red',
                    default     => 'badge-amber',
                }; ?>
                <span class="badge <?= $bc ?>"><?= ucfirst($t['status']) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
