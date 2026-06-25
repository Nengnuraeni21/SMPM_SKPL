<?php
/* =============================================
   SMPM — View: Kelompok Saya (Mahasiswa)
   Variabel: $user, $kelompok, $anggota, $uploads
   ============================================= */
$pageTitle = 'Kelompok Saya';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">
    <div class="page-header">
      <h1>Kelompok Saya</h1>
      <p>Informasi kelompok, anggota, dan dosen pembimbing.</p>
    </div>

    <?php if (!$kelompok): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 12px;opacity:.4">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
      </svg>
      <p>Anda belum terdaftar di kelompok manapun.</p>
    </div>
    <?php else: ?>

    <!-- Info Kelompok -->
    <div class="card mb-16">
      <div class="card-title"><?= htmlspecialchars($kelompok['nama']) ?></div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">
        <div>
          <div class="text-xs text-muted font-600" style="text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Tema Proyek</div>
          <div class="font-600"><?= htmlspecialchars($kelompok['tema']) ?></div>
        </div>
        <div>
          <div class="text-xs text-muted font-600" style="text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Dosen Pembimbing</div>
          <div class="font-600"><?= htmlspecialchars($kelompok['dosen_nama'] ?? '—') ?></div>
        </div>
        <div>
          <div class="text-xs text-muted font-600" style="text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Status</div>
          <span class="badge <?= $kelompok['status'] === 'aktif' ? 'badge-green' : 'badge-navy' ?>">
            <?= ucfirst($kelompok['status']) ?>
          </span>
        </div>
        <div>
          <div class="text-xs text-muted font-600" style="text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Progress</div>
          <div class="flex items-center gap-8">
            <div class="progress-wrap" style="flex:1">
              <div class="progress-fill progress-blue" style="width:<?= $kelompok['progress'] ?>%"></div>
            </div>
            <span class="font-600"><?= $kelompok['progress'] ?>%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Anggota -->
    <div class="card mb-16">
      <div class="card-title">Anggota Kelompok</div>
      <?php if (empty($anggota)): ?>
      <p class="text-muted text-sm">Belum ada anggota terdaftar.</p>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:10px">
        <?php foreach ($anggota as $a): ?>
        <div class="flex items-center gap-12" style="padding:10px;background:var(--surface);border-radius:var(--radius-md)">
          <div class="avatar"><?= htmlspecialchars($a['avatar'] ?? '--') ?></div>
          <div>
            <div class="font-600"><?= htmlspecialchars($a['nama']) ?></div>
            <div class="text-sm text-muted">NIM: <?= htmlspecialchars($a['nim']) ?></div>
          </div>
          <?php if ((int)$a['id'] === (int)$user['id']): ?>
          <span class="badge badge-blue" style="margin-left:auto">Anda</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Upload terbaru -->
    <?php if (!empty($uploads)): ?>
    <div class="card">
      <div class="card-title">File yang Sudah Diupload</div>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>File</th><th>Tugas</th><th>Diupload oleh</th><th>Tanggal</th></tr>
          </thead>
          <tbody>
            <?php foreach ($uploads as $up): ?>
            <tr>
              <td>
                <div class="flex items-center gap-8">
                  <div style="width:28px;height:28px;background:var(--accent-lt);border-radius:6px;
                              display:flex;align-items:center;justify-content:center;
                              font-size:.62rem;font-weight:700;color:var(--accent)">
                    <?= htmlspecialchars($up['tipe']) ?>
                  </div>
                  <span class="text-sm"><?= htmlspecialchars($up['nama_file']) ?></span>
                </div>
              </td>
              <td class="text-sm text-muted"><?= htmlspecialchars($up['tugas_judul'] ?? '—') ?></td>
              <td class="text-sm"><?= htmlspecialchars($up['uploader_nama'] ?? '—') ?></td>
              <td class="text-sm text-muted"><?= htmlspecialchars($up['uploaded_at'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
