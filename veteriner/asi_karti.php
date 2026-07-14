<?php
require_once 'db.php';

// 1. Seçim şeridi için benzersiz tüm hayvanları çekelim
$pets_stmt = $db->query("SELECT MIN(id) as id, pet_name, breed, owner_name FROM patients WHERE (is_hidden = 0 OR is_hidden IS NULL) GROUP BY pet_name, owner_name, breed ORDER BY pet_name ASC");
$pets = $pets_stmt->fetchAll();

// 2. Seçilen bir hayvan var mı kontrolü (Yoksa ilk hayvanı açalım)
$selected_pet_name = isset($_GET['pet_name']) ? $_GET['pet_name'] : ($pets[0]['pet_name'] ?? '');
$selected_owner = isset($_GET['owner_name']) ? $_GET['owner_name'] : ($pets[0]['owner_name'] ?? '');

$vaccine_history = [];
$pet_info = null;

if ($selected_pet_name !== '') {
    // Seçili hayvanın genel bilgileri
    $info_stmt = $db->prepare("SELECT * FROM patients WHERE pet_name = ? AND owner_name = ? LIMIT 1");
    $info_stmt->execute([$selected_pet_name, $selected_owner]);
    $pet_info = $info_stmt->fetch();

    // Bu hayvana ait tüm aşı geçmişi
    $history_stmt = $db->prepare("SELECT * FROM patients WHERE pet_name = ? AND owner_name = ? AND vaccine_name IS NOT NULL AND vaccine_name != '' ORDER BY appointment_date DESC");
    $history_stmt->execute([$selected_pet_name, $selected_owner]);
    $vaccine_history = $history_stmt->fetchAll();
}

include 'header.php';
?>

<!-- Hayvan Seçim Şeridi (Yatay Butonlar) -->
<div class="mb-4">
    <small class="text-muted d-block mb-2 fw-bold"><i class="fa-solid fa-paw me-1"></i>AŞI KARTINI GÖRMEK İÇİN HAYVAN SEÇİN:</small>
    <div class="d-flex gap-2 overflow-auto pb-2" style="white-space: nowrap;">
        <?php foreach ($pets as $p): 
            $is_active = ($p['pet_name'] === $selected_pet_name && $p['owner_name'] === $selected_owner);
        ?>
            <a href="asi_karti.php?pet_name=<?= urlencode($p['pet_name']) ?>&owner_name=<?= urlencode($p['owner_name']) ?>" 
               class="btn rounded-pill px-4 py-2 text-start shadow-sm <?= $is_active ? 'btn-primary' : 'btn-white bg-white border text-dark' ?>">
                <strong class="d-block" style="font-size: 14px;"><i class="fa-solid fa-circle me-1" style="font-size: 9px; color: <?= $is_active ? '#fff' : '#00e6a8' ?>;"></i> <?= htmlspecialchars($p['pet_name']) ?></strong>
                <span class="text-xs opacity-75" style="font-size: 11px;"><?= htmlspecialchars($p['owner_name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($pet_info): ?>
    <!-- Dijital Aşı Pasaportu Tasarımı -->
    <div class="card shadow border-0 rounded-4 overflow-hidden mb-5">
        <!-- Pasaport Başlığı (Koyu, Neon Aksanlı Modern Alan) -->
        <div class="bg-dark text-white p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <span class="badge text-uppercase px-3 py-2 mb-3 tracking-wider" style="background:linear-gradient(90deg,#ff2ec4,#00c2ff);">Klinik Aşı Karnesi</span>
                    <h2 class="fw-extrabold m-0 text-white display-5"><?= htmlspecialchars($pet_info['pet_name']) ?></h2>
                    <p class="m-0 mt-2 text-white-50">
                        <i class="fa-solid fa-dog me-1"></i> <?= htmlspecialchars($pet_info['animal_type']) ?> 
                        • <i class="fa-solid fa-dna ms-2 me-1"></i> <?= htmlspecialchars($pet_info['breed'] ?: 'Irk Belirtilmemiş') ?>
                        • <i class="fa-solid fa-venus-mars ms-2 me-1"></i> <?= htmlspecialchars($pet_info['gender'] ?? 'Bilinmiyor') ?>
                    </p>
                </div>
                <div class="text-md-end p-3 rounded-3" style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);">
                    <small class="text-uppercase text-white-50 d-block fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">HASTA YAKINI</small>
                    <span class="fs-5 fw-bold text-white"><?= htmlspecialchars($pet_info['owner_name']) ?></span>
                    <small class="d-block text-white-50"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($pet_info['phone']) ?></small>
                </div>
            </div>
        </div>

        <!-- Aşı Geçmişi Listesi (Timeline Tarzı) -->
        <div class="card-body p-4 p-md-5 bg-white">
            <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clock-rotate-left me-2" style="color:#00c2ff;"></i>Aşı Geçmişi Kronolojisi</h4>
            
            <?php if (count($vaccine_history) > 0): ?>
                <div class="row row-cols-1 g-3">
                    <?php foreach ($vaccine_history as $history): ?>
                        <div class="col">
                            <div class="p-4 rounded-4 border bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 hover-shadow transition">
                                <div class="d-flex align-items-center">
                                    <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background:linear-gradient(135deg,#00e6a8,#049e73);">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold m-0 text-dark"><?= htmlspecialchars($history['vaccine_name']) ?></h5>
                                        <small class="text-muted"><i class="fa-solid fa-calendar-day me-1"></i> Uygulanma Tarihi: <strong><?= date('d.m.Y', strtotime($history['appointment_date'])) ?></strong></small>
                                    </div>
                                </div>
                                <div class="text-md-end">
                                    <small class="text-muted d-block mb-1" style="font-size: 11px; font-weight: 600;">GELECEK DOZ</small>
                                    <span class="badge bg-success px-3 py-2 rounded-pill font-monospace" style="font-size: 13px;">
                                        <?= $history['next_dose_date'] ? date('d.m.Y', strtotime($history['next_dose_date'])) : 'Gerekmiyor' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted border border-dashed rounded-4">
                    <i class="fa-solid fa-shield-virus fs-1 mb-3" style="color:#ff2ec4;"></i>
                    <p class="m-0 fs-5">Bu hayvana ait henüz yapılmış bir aşı kaydı bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center border-0 shadow-sm rounded-4 py-4">
        Görüntülenecek evcil hayvan bulunamadı. Lütfen önce sisteme bir randevu veya hasta kaydı ekleyin.
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
