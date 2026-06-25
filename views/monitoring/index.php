<?php
/* =============================================
   SMPM — View: Monitoring Kelompok (Dosen)
   Variabel: $user, $kelompokList, $tugasList, $uploads
   ============================================= */
$pageTitle = 'Monitoring Kelompok';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">
    <div class="page-header">
      <h1>Monitoring Kelompok</h1>
      <p>Pantau perkembangan detail semua kelompok bimbingan Anda.</p>
    </div>

    <?php
    // Kelompokkan tugas per kelompok_id
    $tugasByKelompok = [];
    foreach ($tugasList as $t) {
        $tugasByKelompok[$t['kelompok_id']][] = $t;
    }
    // Kelompokkan upload per kelompok_id
    $uploadsByKelompok = [];
    foreach ($uploads as $up) {
        $uploadsByKelompok[$up['kelompok_id']][] = $up;
    }
    ?>

    <?php foreach ($kelompokList as $k): ?>
    <?php
    $kTugas   = $tugasByKelompok[$k['id']] ?? [];
    $kUploads = $uploadsByKelompok[$k['id']] ?? [];
    $selesai  = count(array_filter($kTugas, fn($t) => $t['status'] === 'selesai'));
    $total    = count($kTugas);
    ?>
    <div class="card mb-16">
      <!-- Header -->
      <div class="flex justify-between items-center mb-12" style="flex-wrap:wrap;gap:10px">
        <div>
          <div class="card-title" style="margin:0"><?= htmlspecialchars($k['nama']) ?></div>
          <div class="text-sm text-muted mt-4"><?= htmlspecialchars($k['tema']) ?></div>
        </div>
        <div class="flex gap-8 items-center">
          <span class="badge <?= $k['status'] === 'aktif' ? 'badge-green' : 'badge-navy' ?>"><?= ucfirst($k['status']) ?></span>
          <span class="badge badge-blue"><?= $total ?> tugas</span>
          <span class="badge badge-green"><?= $selesai ?> selesai</span>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="flex items-center gap-12 mb-16">
        <div class="progress-wrap" style="flex:1">
          <?php $pc = $k['progress'] >= 70 ? 'progress-green' : ($k['progress'] >= 40 ? 'progress-blue' : 'progress-amber'); ?>
          <div class="progress-fill <?= $pc ?>" style="width:<?= $k['progress'] ?>%"></div>
        </div>
        <span class="font-600"><?= $k['progress'] ?>%</span>
      </div>

      <!-- Tabel tugas -->
      <?php if (!empty($kTugas)): ?>
      <div class="text-sm font-600 mb-8" style="color:var(--text-2)">Daftar Tugas</div>
      <div class="table-wrap mb-12">
        <table class="data-table">
          <thead>
            <tr><th>Judul</th><th>Deadline</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($kTugas as $t): ?>
            <tr>
              <td class="text-sm"><?= htmlspecialchars($t['judul']) ?></td>
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
      <?php endif; ?>

      <!-- Upload terbaru -->
      <?php if (!empty($kUploads)): ?>
      <div class="text-sm font-600 mb-8" style="color:var(--text-2)">File Terupload (<?= count($kUploads) ?>)</div>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        <?php foreach (array_slice($kUploads, 0, 5) as $up): ?>
        <div style="display:flex;align-items:center;gap:8px;padding:6px 12px;
                    background:var(--surface);border-radius:var(--radius-md);
                    border:1px solid var(--border);font-size:.78rem">
          <div style="width:22px;height:22px;background:var(--accent-lt);border-radius:4px;
                      display:flex;align-items:center;justify-content:center;
                      font-size:.6rem;font-weight:700;color:var(--accent)">
            <?= htmlspecialchars($up['tipe']) ?>
          </div>
          <span><?= htmlspecialchars($up['nama_file']) ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (count($kUploads) > 5): ?>
        <span class="text-sm text-muted" style="padding:6px 0">+<?= count($kUploads)-5 ?> lainnya</span>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
