<?php
require_once 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

// Sadece aşısı olan ve gizlenmemiş kayıtları getiriyoruz
$sql = "SELECT * FROM patients WHERE vaccine_name IS NOT NULL AND vaccine_name != '' AND (is_hidden = 0 OR is_hidden IS NULL)";

if ($search !== '') {
    $sql .= " AND (pet_name LIKE ? OR owner_name LIKE ? OR vaccine_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY appointment_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$vaccines = $stmt->fetchAll();

include 'header.php';
?>

<!-- Üst Arama Çubuğu -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
    <form method="GET" action="asilar.php" class="w-100" style="max-width: 600px;">
        <div class="input-group shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0 text-muted ps-4"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" class="form-control border-0 py-2 shadow-none" placeholder="Aşı veya hasta ara..." value="<?= htmlspecialchars($search) ?>">
        </div>
    </form>
    <a href="ekle.php" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm fw-semibold"><i class="fa-solid fa-plus me-1"></i> Yeni Aşı Kaydı</a>
</div>

<!-- Aşı Kartları Grid -->
<div class="row row-cols-1 row-cols-lg-2 g-4">
    <?php if (count($vaccines) > 0): ?>
        <?php foreach ($vaccines as $vac): ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 20px; background:#00c2ff1a; color:#0091c2;">
                                <i class="fa-solid fa-syringe"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($vac['pet_name']) ?></h5>
                                <small class="text-muted"><?= htmlspecialchars($vac['owner_name']) ?></small>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 fw-semibold bg-success-subtle text-success border border-success-subtle">Uygulandı</span>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-2">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px; letter-spacing: 0.5px;">UYGULANAN AŞI</small>
                        <span class="text-dark fw-bold" style="font-size: 15px;"><i class="fa-solid fa-shield-dog me-1" style="color:#ff2ec4;"></i> <?= htmlspecialchars($vac['vaccine_name']) ?></span>
                    </div>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px;">AŞI TARİHİ</small>
                                <span class="text-dark font-monospace" style="font-size: 14px;"><?= date('d.m.Y', strtotime($vac['appointment_date'])) ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3 h-100">
                                <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px;">SONRAKİ DOZ TARİHİ</small>
                                <span class="fw-bold font-monospace" style="font-size: 14px; color:#ff3d81;">
                                    <?= $vac['next_dose_date'] ? date('d.m.Y', strtotime($vac['next_dose_date'])) : 'Gerekmiyor' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex text-muted mt-auto border-top pt-2" style="font-size: 13px;">
                        <span class="me-3">Tür: <strong class="text-dark"><?= htmlspecialchars($vac['animal_type']) ?></strong></span>
                        <span>Kilo: <strong class="text-dark"><?= htmlspecialchars($vac['weight'] ?? '-') ?> kg</strong></span>
                        <a href="guncelle.php?id=<?= $vac['id'] ?>" class="ms-auto text-decoration-none" style="color:#00c2ff;"><i class="fa-solid fa-marker"></i> Düzenle</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 w-100">
            <div class="alert alert-info text-center border-0 shadow-sm rounded-4 py-4 w-100">
                <i class="fa-solid fa-syringe fs-2 text-muted mb-3 d-block"></i>
                <p class="m-0 text-muted">Sistemde henüz aşı kaydı bulunmuyor.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
