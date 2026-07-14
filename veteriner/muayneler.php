<?php
require_once 'db.php';

// Arama filtresi
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

// Sadece gizlenmemiş olan hastaları getirelim (Eğer is_hidden sütunu yoksa WHERE 1=1 kalsın)
$sql = "SELECT * FROM patients WHERE (is_hidden = 0 OR is_hidden IS NULL)";

if ($search !== '') {
    $sql .= " AND (pet_name LIKE ? OR owner_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY appointment_date DESC, appointment_time DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$examinations = $stmt->fetchAll();

include 'header.php';
?>

<!-- Üst Kısım: Arama ve Yeni Butonu -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
    <form method="GET" action="muayneler.php" class="w-100" style="max-width: 600px;">
        <div class="input-group shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0 text-muted ps-4"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" name="search" class="form-control border-0 py-2 shadow-none" placeholder="Muayene ara..." value="<?= htmlspecialchars($search) ?>">
        </div>
    </form>
    <a href="ekle.php" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm fw-semibold"><i class="fa-solid fa-plus me-1"></i> Yeni Muayene</a>
</div>

<!-- Muayene Kartları (Grid Yapısı) -->
<div class="row row-cols-1 row-cols-lg-2 g-4">
    <?php if (count($examinations) > 0): ?>
        <?php foreach ($examinations as $exam): 
            // Duruma göre badge rengi ve metni belirleme (Görseldeki gibi)
            if ($exam['status'] == 'taburcu') {
                $badge_class = 'bg-success-subtle text-success border border-success-subtle';
                $badge_text = 'Tamamlandı';
            } elseif ($exam['status'] == 'bekliyor') {
                $badge_class = 'bg-warning-subtle text-warning border border-warning-subtle';
                $badge_text = 'Bekliyor';
            } else {
                $badge_class = 'bg-info-subtle text-info border border-info-subtle';
                $badge_text = 'Devam ediyor';
            }
        ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                    
                    <!-- Kart Başlığı -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 20px; background:#ff2ec41a; color:#d6169f;">
                                <i class="fa-solid fa-stethoscope"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($exam['pet_name']) ?></h5>
                                <small class="text-muted"><?= htmlspecialchars($exam['owner_name']) ?> • <?= date('Y-m-d', strtotime($exam['appointment_date'])) ?></small>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 fw-semibold <?= $badge_class ?>"><?= $badge_text ?></span>
                    </div>

                    <!-- Gri Kutu Bölümleri -->
                    <div class="bg-light rounded-3 p-3 mb-2">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px; letter-spacing: 0.5px;">ŞİKAYET</small>
                        <span class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($exam['complaint'] ?: 'Belirtilmemiş') ?></span>
                    </div>
                    
                    <div class="bg-light rounded-3 p-3 mb-2">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px; letter-spacing: 0.5px;">TANI</small>
                        <span class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($exam['diagnosis'] ?: 'Henüz tanı girilmedi.') ?></span>
                    </div>
                    
                    <div class="bg-light rounded-3 p-3 mb-4">
                        <small class="text-muted fw-bold d-block mb-1" style="font-size: 11px; letter-spacing: 0.5px;">TEDAVİ</small>
                        <span class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($exam['treatment'] ?: 'Henüz tedavi girilmedi.') ?></span>
                    </div>

                    <!-- Kart Altı İstatistikleri -->
                    <div class="d-flex text-muted mt-auto" style="font-size: 13px;">
                        <span class="me-4">Ağırlık <strong class="text-dark fw-semibold"><?= htmlspecialchars($exam['weight'] ?? 'Bilinmiyor') ?> <?= ($exam['weight'] != 'Bilinmiyor' && $exam['weight'] != '') ? 'kg' : '' ?></strong></span>
                        <span>Ateş <strong class="text-dark fw-semibold"><?= htmlspecialchars($exam['temperature'] ?? 'Bilinmiyor') ?> <?= ($exam['temperature'] != 'Bilinmiyor' && $exam['temperature'] != '') ? '°C' : '' ?></strong></span>
                        
                        <!-- Düzenle Butonu (Opsiyonel olarak sağ alta ekledim, muayene sonucunu girmek için işine yarar) -->
                        <a href="guncelle.php?id=<?= $exam['id'] ?>" class="ms-auto text-decoration-none" style="color:#00c2ff;"><i class="fa-solid fa-pen"></i> Düzenle</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 w-100">
            <div class="alert alert-info text-center border-0 shadow-sm rounded-4 py-4 w-100">
                <i class="fa-solid fa-notes-medical fs-2 text-muted mb-3 d-block"></i>
                <p class="m-0 fs-5 text-muted">Arama kriterlerinize uygun muayene bulunamadı.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
