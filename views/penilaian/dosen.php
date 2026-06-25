<?php
/* =============================================
   SMPM — View: Penilaian (Dosen)
   Variabel: $user, $kelompokList, $penilaianList
   ============================================= */
$pageTitle = 'Penilaian';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="main-content">
  <div class="page-body">
    <div class="page-header">
      <h1>Penilaian Kelompok</h1>
      <p>Berikan nilai dan feedback untuk setiap kelompok bimbingan.</p>
    </div>

    <?php
    // Buat map penilaian by kelompok_id untuk akses cepat
    $nilaiMap = [];
    foreach ($penilaianList as $p) {
        $nilaiMap[$p['kelompok_id']] = $p;
    }
    ?>

    <?php foreach ($kelompokList as $k): ?>
    <?php $p = $nilaiMap[$k['id']] ?? null; ?>
    <div class="card mb-16">
      <div class="flex justify-between items-center mb-16" style="flex-wrap:wrap;gap:10px">
        <div>
          <div class="card-title" style="margin:0"><?= htmlspecialchars($k['nama']) ?></div>
          <div class="text-sm text-muted mt-4"><?= htmlspecialchars($k['tema']) ?></div>
        </div>
        <?php if ($p && $p['nilai'] !== null): ?>
        <div style="text-align:right">
          <div style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--accent)">
            <?= $p['nilai'] ?>
          </div>
          <div class="text-xs text-muted">Nilai saat ini</div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Form penilaian -->
      <form method="POST" action="index.php?action=storePenilaian">
        <input type="hidden" name="kelompok_id" value="<?= $k['id'] ?>" />
        <div style="display:grid;grid-template-columns:120px 1fr;gap:14px;align-items:start">
          <div class="form-group">
            <label class="form-label">Nilai (0-100)</label>
            <input name="nilai" class="form-control" type="number"
                   min="0" max="100" required
                   value="<?= htmlspecialchars($p['nilai'] ?? '') ?>"
                   placeholder="85" />
          </div>
          <div class="form-group">
            <label class="form-label">Feedback / Catatan</label>
            <textarea name="feedback" class="form-control" rows="2"
                      placeholder="Berikan feedback konstruktif untuk kelompok..."
                      style="resize:vertical"><?= htmlspecialchars($p['feedback'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="flex gap-8 mt-8">
          <button type="submit" class="btn btn-primary btn-sm">
            <?= ($p && $p['nilai'] !== null) ? '✓ Perbarui Nilai' : '+ Beri Nilai' ?>
          </button>
          <?php if ($p && $p['nilai'] !== null): ?>
          <span class="text-xs text-muted" style="align-self:center">
            Terakhir dinilai: <?= htmlspecialchars($p['dinilai_at'] ?? '') ?>
          </span>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <?php endforeach; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
