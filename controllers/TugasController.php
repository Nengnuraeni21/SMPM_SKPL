<?php
/* =============================================
   SMPM — Controller: Tugas
   ============================================= */

require_once __DIR__ . '/../models/Tugas.php';
require_once __DIR__ . '/../models/Upload.php';
require_once __DIR__ . '/../models/Kelompok.php';

class TugasController {
    private Tugas    $tugasModel;
    private Upload   $uploadModel;
    private Kelompok $kelompokModel;

    private const ALLOWED_MIME = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'image/png',
        'image/jpeg',
    ];
    private const MAX_SIZE  = 10 * 1024 * 1024; // 10 MB
    private const UPLOAD_DIR = __DIR__ . '/../uploads/';

    public function __construct() {
        $this->tugasModel    = new Tugas();
        $this->uploadModel   = new Upload();
        $this->kelompokModel = new Kelompok();
    }

    /* ===== Mahasiswa: lihat tugas ===== */
    public function index(): void {
        $this->requireRole(['mahasiswa']);
        $user      = $_SESSION['user'];
        $tugasList = $this->tugasModel->getByKelompok((int) $user['kelompok_id']);
        // Pass uploads per tugas agar view bisa cek apakah sudah ada file
        $uploads   = $this->uploadModel->getByKelompok((int) $user['kelompok_id']);
        require_once __DIR__ . '/../views/tugas/index.php';
    }

    /* ===== Dosen: kelola tugas ===== */
    public function dosenIndex(): void {
        $this->requireRole(['dosen']);
        $kelompokList = $this->kelompokModel->getAll();
        $tugasList    = $this->tugasModel->getAllWithKelompok();
        require_once __DIR__ . '/../views/tugas/dosen.php';
    }

    /* ===== Dosen: tambah tugas (POST) ===== */
    public function store(): void {
        $this->requireRole(['dosen']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=tugasDosen');
            exit;
        }

        $judul      = trim($_POST['judul'] ?? '');
        $kelompokId = (int) ($_POST['kelompok_id'] ?? 0);
        $assigneeId = (int) ($_POST['assignee_id'] ?? 0) ?: null;
        $deadline   = $_POST['deadline'] ?? '';
        $deskripsi  = trim($_POST['deskripsi'] ?? '');

        if (empty($judul) || !$kelompokId || empty($deadline)) {
            $_SESSION['flash_error'] = 'Judul, kelompok, dan deadline wajib diisi.';
            header('Location: index.php?page=tugasDosen');
            exit;
        }

        $this->tugasModel->create([
            'judul'       => $judul,
            'deskripsi'   => $deskripsi,
            'kelompok_id' => $kelompokId,
            'assignee_id' => $assigneeId,
            'deadline'    => $deadline,
            'status'      => 'pending',
            'created_by'  => $_SESSION['user']['id'],
        ]);

        $_SESSION['flash_success'] = 'Tugas berhasil ditambahkan.';
        header('Location: index.php?page=tugasDosen');
        exit;
    }

    /* ===== Mahasiswa: upload file & kumpulkan tugas (POST, redirect) ===== */
    public function upload(): void {
        $this->requireRole(['mahasiswa']);
        $user    = $_SESSION['user'];
        $tugasId = (int) ($_POST['tugas_id'] ?? 0);

        if (!$tugasId) {
            $_SESSION['flash_error'] = 'Tugas tidak valid.';
            header('Location: index.php?page=tugas');
            exit;
        }

        $tugas = $this->tugasModel->findById($tugasId);
        if (!$tugas || (int) $tugas['kelompok_id'] !== (int) $user['kelompok_id']) {
            $_SESSION['flash_error'] = 'Akses ditolak.';
            header('Location: index.php?page=tugas');
            exit;
        }

        // Cek file ada
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errMsg = $this->uploadErrorMessage($_FILES['file']['error'] ?? -1);
            $_SESSION['flash_error'] = 'Upload gagal: ' . $errMsg;
            header('Location: index.php?page=tugas');
            exit;
        }

        $file = $_FILES['file'];

        // Validasi ukuran
        if ($file['size'] > self::MAX_SIZE) {
            $_SESSION['flash_error'] = 'Ukuran file melebihi batas 10 MB.';
            header('Location: index.php?page=tugas');
            exit;
        }

        // Validasi tipe MIME
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
            $_SESSION['flash_error'] = 'Tipe file tidak diizinkan. Gunakan PDF, DOC, DOCX, ZIP, PNG, atau JPG.';
            header('Location: index.php?page=tugas');
            exit;
        }

        // Buat folder uploads jika belum ada
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Nama file aman & unik
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $saveName = uniqid('file_', true) . '.' . $ext;
        $savePath = self::UPLOAD_DIR . $saveName;

        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            $_SESSION['flash_error'] = 'Gagal menyimpan file. Coba lagi.';
            header('Location: index.php?page=tugas');
            exit;
        }

        // Simpan record ke DB
        $this->uploadModel->create([
            'nama_file'   => htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'),
            'path_file'   => 'uploads/' . $saveName,
            'ukuran'      => $file['size'],
            'tipe'        => strtoupper($ext),
            'kelompok_id' => (int) $user['kelompok_id'],
            'user_id'     => (int) $user['id'],
            'tugas_id'    => $tugasId,
        ]);

        // Tandai tugas selesai
        $this->tugasModel->updateStatus($tugasId, 'selesai');

        $_SESSION['flash_success'] = 'Tugas "' . htmlspecialchars($tugas['judul']) . '" berhasil dikumpulkan!';
        header('Location: index.php?page=tugas');
        exit;
    }

    /* ===== Dosen: hapus tugas (POST) ===== */
    public function delete(): void {
        $this->requireRole(['dosen']);
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $_SESSION['flash_error'] = 'ID tugas tidak valid.';
            header('Location: index.php?page=tugasDosen');
            exit;
        }
        $this->tugasModel->delete($id);
        $_SESSION['flash_success'] = 'Tugas berhasil dihapus.';
        header('Location: index.php?page=tugasDosen');
        exit;
    }

    /* ===== Update status tugas via AJAX (JSON) ===== */
    public function updateStatus(): void {
        $this->requireRole(['mahasiswa', 'dosen']);
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$id || !$status) {
            $this->jsonResponse(['ok' => false, 'message' => 'Parameter tidak valid.'], 400);
        }

        $ok = $this->tugasModel->updateStatus($id, $status);
        $this->jsonResponse(['ok' => $ok]);
    }

    /* ---------- helpers ---------- */

    private function requireRole(array $roles): void {
        if (empty($_SESSION['user']) || !in_array($_SESSION['user']['role'], $roles, true)) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    private function jsonResponse(array $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function uploadErrorMessage(int $code): string {
        return match($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File terlalu besar.',
            UPLOAD_ERR_PARTIAL   => 'Upload tidak lengkap.',
            UPLOAD_ERR_NO_FILE   => 'Tidak ada file yang dipilih.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file.',
            default              => 'Error tidak diketahui (kode ' . $code . ').',
        };
    }
}
