<?php
/* =============================================
   SMPM — View: Nilai Saya (Mahasiswa)
   Variabel: $user, $kelompok, $penilaian
   ============================================= */
$pageTitle = 'Nilai Saya';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">
    <div class="page-header">
      <h1>Nilai Saya</h1>
      <p>Lihat nilai dan feedback dari dosen pembimbing untuk kelompok Anda.</p>
    </div>

    <?php if (!$kelompok): ?>
    <div class="card">
      <p class="text-muted">Anda belum terdaftar di kelompok manapun.</p>
    </div>
    <?php elseif (!$penilaian || $penilaian['nilai'] === null): ?>
    <div class="card">
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 12px;opacity:.4">
          <circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
        </svg>
        <p>Belum ada penilaian dari dosen untuk kelompok Anda.</p>
      </div>
    </div>
    <?php else: ?>

    <div class="card mb-16">
      <div class="card-title"><?= htmlspecialchars($kelompok['nama']) ?></div>
      <div style="font-size:.875rem;color:var(--text-2);margin-bottom:20px">
        <?= htmlspecialchars($kelompok['tema']) ?>
      </div>

      <!-- Nilai besar -->
      <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;
                  background:linear-gradient(135deg,var(--navy),var(--navy-mid));
                  border-radius:var(--radius-lg);padding:24px 28px;margin-bottom:20px">
        <div style="text-align:center">
          <div style="font-family:var(--font-display);font-size:4rem;font-weight:800;color:#fff;line-height:1">
            <?= $penilaian['nilai'] ?>
          </div>
          <div style="font-size:.75rem;color:rgba(255,255,255,.5);margin-top:4px;text-transform:uppercase;letter-spacing:.06em">Nilai Akhir</div>
        </div>
        <div style="flex:1;min-width:180px">
          <!-- Bar visual nilai -->
          <div style="height:10px;background:rgba(255,255,255,.15);border-radius:10px;overflow:hidden;margin-bottom:8px">
            <div style="height:100%;width:<?= $penilaian['nilai'] ?>%;background:<?= $penilaian['nilai'] >= 80 ? '#16a34a' : ($penilaian['nilai'] >= 60 ? '#2f80ed' : '#d97706') ?>;border-radius:10px;transition:width .6s"></div>
          </div>
          <div style="font-size:.8rem;color:rgba(255,255,255,.7)">
            <?= $penilaian['nilai'] >= 80 ? '🎉 Sangat Baik' : ($penilaian['nilai'] >= 70 ? '👍 Baik' : ($penilaian['nilai'] >= 60 ? '✅ Cukup' : '📚 Perlu Peningkatan')) ?>
          </div>
          <div style="font-size:.75rem;color:rgba(255,255,255,.4);margin-top:4px">
            Dinilai oleh: <?= htmlspecialchars($penilaian['dosen_nama'] ?? '—') ?>
          </div>
        </div>
      </div>

      <!-- Feedback -->
      <?php if (!empty($penilaian['feedback'])): ?>
      <div>
        <div class="text-sm font-600 mb-8" style="color:var(--text-2)">💬 Feedback Dosen</div>
        <div style="background:var(--surface);border-radius:var(--radius-md);padding:16px 18px;
                    border-left:4px solid var(--accent);font-size:.9rem;line-height:1.7;color:var(--text-1)">
          <?= nl2br(htmlspecialchars($penilaian['feedback'])) ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
