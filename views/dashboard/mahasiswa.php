<?php
/* =============================================
   SMPM — View: Dashboard Mahasiswa
   Variabel dari DashboardController::dataMahasiswa():
   $user, $tugasList, $kelompok, $anggota, $uploads,
   $penilaian, $selesai, $proses, $terlambat,
   $pending, $total, $completion
   ============================================= */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">

    <div class="page-header">
      <h1>Selamat Datang, <?= htmlspecialchars($user['nama']) ?> 👋</h1>
      <p>Ringkasan aktivitas proyek Anda. Completion rate: <strong><?= $completion ?>%</strong></p>
    </div>

    <!-- Stats -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Total Tugas</div>
        <div class="stat-value"><?= $total ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Selesai</div>
        <div class="stat-value" style="color:var(--success)"><?= $selesai ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Sedang Proses</div>
        <div class="stat-value" style="color:var(--accent)"><?= $proses ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Terlambat</div>
        <div class="stat-value" style="color:var(--danger)"><?= $terlambat ?></div>
      </div>
    </div>

    <!-- Progress kelompok -->
    <?php if ($kelompok): ?>
    <div class="card mt-16">
      <div class="card-title">Progress <?= htmlspecialchars($kelompok['nama']) ?></div>
      <div style="font-size:.875rem;color:var(--text-2);margin-bottom:10px">
        <?= htmlspecialchars($kelompok['tema']) ?>
      </div>
      <div class="flex items-center gap-16 mb-8">
        <div class="progress-wrap" style="flex:1">
          <div class="progress-fill progress-blue" style="width:<?= $kelompok['progress'] ?>%"></div>
        </div>
        <span style="font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--accent)">
          <?= $kelompok['progress'] ?>%
        </span>
      </div>
      <?php if (!empty($anggota)): ?>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
        <div class="text-sm font-600 mb-8">Anggota Kelompok</div>
        <div style="display:flex;gap:6px;flex-wrap:wrap">
          <?php foreach ($anggota as $a): ?>
          <div class="flex items-center gap-6" style="background:var(--surface);border-radius:20px;padding:4px 10px;font-size:.8rem">
            <div class="avatar" style="width:22px;height:22px;font-size:.6rem"><?= htmlspecialchars($a['avatar'] ?? '--') ?></div>
            <?= htmlspecialchars($a['nama']) ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Tugas terbaru -->
    <div class="card mt-16">
      <div class="flex justify-between items-center mb-16">
        <div class="card-title" style="margin:0">Tugas Terbaru</div>
        <a href="index.php?page=tugas" class="btn btn-outline btn-sm">Lihat Semua →</a>
      </div>
      <?php if (empty($tugasList)): ?>
      <p class="text-muted text-sm">Belum ada tugas.</p>
      <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Judul Tugas</th><th>Deadline</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($tugasList, 0, 5) as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['judul']) ?></td>
              <td class="text-sm"><?= date('d M Y', strtotime($t['deadline'])) ?></td>
              <td>
                <?php $sc = match($t['status']) {
                    'selesai'   => 'badge-green',
                    'proses'    => 'badge-blue',
                    'terlambat' => 'badge-red',
                    default     => 'badge-amber',
                }; ?>
                <span class="badge <?= $sc ?>"><?= ucfirst($t['status']) ?></span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- Nilai dari dosen -->
    <?php if ($penilaian && !empty($penilaian['nilai'])): ?>
    <div class="card mt-16">
      <div class="card-title">📊 Nilai dari Dosen</div>
      <div class="flex items-center gap-24" style="flex-wrap:wrap">
        <div style="text-align:center">
          <div style="font-family:var(--font-display);font-size:2.5rem;font-weight:800;color:var(--accent)">
            <?= $penilaian['nilai'] ?>
          </div>
          <div class="text-sm text-muted">Nilai</div>
        </div>
        <?php if (!empty($penilaian['feedback'])): ?>
        <div style="flex:1;min-width:200px;background:var(--surface);border-radius:var(--radius-md);padding:14px;
                    border-left:3px solid var(--accent);font-size:.88rem;color:var(--text-2)">
          <?= htmlspecialchars($penilaian['feedback']) ?>
          <div class="text-xs text-muted mt-8">— <?= htmlspecialchars($penilaian['dosen_nama'] ?? 'Dosen') ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
