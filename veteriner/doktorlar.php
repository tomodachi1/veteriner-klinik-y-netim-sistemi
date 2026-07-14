<?php
require_once 'db.php';

// Yeni Doktor Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $full_name = trim($_POST['full_name']);
    $specialty = trim($_POST['specialty']);
    $phone = trim($_POST['phone']);

    if (!empty($full_name)) {
        $stmt = $db->prepare("INSERT INTO doctors (full_name, specialty, phone) VALUES (?, ?, ?)");
        $stmt->execute([$full_name, $specialty, $phone]);
        header("Location: doktorlar.php?msg=added");
        exit;
    }
}

// Doktor Silme İşlemi
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $db->prepare("DELETE FROM doctors WHERE id = ?")->execute([$del_id]);
    // Bu doktora ait hastaların doctor_id'sini NULL yapalım ki sistem çökmesin
    $db->prepare("UPDATE patients SET doctor_id = NULL WHERE doctor_id = ?")->execute([$del_id]);
    header("Location: doktorlar.php?msg=deleted");
    exit;
}

$doctors = $db->query("SELECT * FROM doctors ORDER BY full_name ASC")->fetchAll();

include 'header.php';
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-extrabold m-0 text-dark"><i class="fa-solid fa-user-doctor text-primary me-2"></i>Klinik Hekimleri</h4>
            <small class="text-muted">Sistemdeki doktorları yönetin ve branşlarını belirleyin.</small>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
            <i class="fa-solid fa-plus me-1"></i> Yeni Hekim Ekle
        </button>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><i class="fa-solid fa-check me-2"></i> Doktor başarıyla eklendi!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="fa-solid fa-trash me-2"></i> Doktor sistemden silindi.</div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
        <?php if(count($doctors) > 0): ?>
            <?php foreach ($doctors as $doc): ?>
                <div class="col">
                    <div class="card h-100 border-0 p-4" style="border-radius: 20px;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold me-3 shadow-sm" 
                                 style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--neon-pink) 100%); font-size: 24px;">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold m-0 text-dark"><?= htmlspecialchars($doc['full_name']) ?></h5>
                                <span class="badge bg-info mt-1"><?= htmlspecialchars($doc['specialty']) ?></span>
                            </div>
                        </div>
                        <div class="text-muted mb-4" style="font-size: 14px;">
                            <i class="fa-solid fa-phone me-2"></i> <?= htmlspecialchars($doc['phone'] ?: 'Telefon belirtilmedi') ?>
                        </div>
                        <div class="mt-auto pt-3 border-top text-end">
                            <a href="doktorlar.php?delete_id=<?= $doc['id'] ?>" class="btn btn-sm text-danger fw-bold hover-bg-light rounded-pill px-3" onclick="return confirm('Bu doktoru silmek istediğinize emin misiniz?')"><i class="fa-solid fa-trash me-1"></i> Sil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 w-100 text-center py-5">
                <i class="fa-solid fa-user-md fs-1 text-muted mb-3 d-block"></i>
                <p class="text-muted">Henüz sisteme eklenmiş bir doktor bulunmuyor.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i>Yeni Hekim Kaydı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="doktorlar.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px;">AD SOYAD</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Örn: Dr. Ahmet Yılmaz">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px;">UZMANLIK / BRANŞ</label>
                        <input type="text" name="specialty" class="form-control" value="Veteriner Hekim">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold" style="font-size: 11px;">TELEFON</label>
                        <input type="text" name="phone" class="form-control" placeholder="Örn: 0555...">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_doctor" class="btn btn-primary rounded-pill px-4">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>