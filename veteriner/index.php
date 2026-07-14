<?php
// index.php
require_once 'db.php'; 
require_once 'header.php';

// 1. İstatistikleri Çekelim (Artık 'patients' tablosundan çekiyoruz)
$total_patients = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();
// Bugünkü randevuları saymak için CURDATE() kullanıyoruz
$today_appointments = $db->query("SELECT COUNT(*) FROM patients WHERE appointment_date = CURDATE()")->fetchColumn(); 
// Bekleyen muayeneleri sayıyoruz
$pending_exams = $db->query("SELECT COUNT(*) FROM patients WHERE status = 'bekliyor'")->fetchColumn();

// 2. BUGÜNKÜ RANDEVULARI ÇEKELİM (Sadece bugünün randevularını saate göre sıralayarak listeliyoruz)
$stmt = $db->query("
    SELECT * 
    FROM patients 
    WHERE appointment_date = CURDATE()
    ORDER BY appointment_time ASC
");
$appointments = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="row g-4 mb-5 justify-content-center">
        <!-- İstatistik Kartları -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 rounded-4 card-counter">
                <div class="card-body text-center">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg, rgba(255,46,196,.15), rgba(0,194,255,.15)); color:#d6169f; font-size:1.3rem;">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <h6 class="text-muted text-uppercase fs-7">Toplam Kayıtlı Hasta</h6>
                    <h2 class="fw-bold text-dark m-0 mt-2"><?= $total_patients ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 rounded-4 card-counter">
                <div class="card-body text-center">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg, rgba(0,194,255,.15), rgba(0,230,168,.15)); color:#0091c2; font-size:1.3rem;">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <h6 class="text-muted text-uppercase fs-7">Bugünkü Randevu</h6>
                    <h2 class="fw-bold text-dark m-0 mt-2"><?= $today_appointments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 rounded-4 card-counter">
                <div class="card-body text-center">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg, rgba(0,230,168,.18), rgba(255,46,196,.15)); color:#049e73; font-size:1.3rem;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <h6 class="text-muted text-uppercase fs-7">Bekleyen Muayene</h6>
                    <h2 class="fw-bold text-dark m-0 mt-2"><?= $pending_exams ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Randevu Listesi -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-calendar-day me-2" style="color:#00c2ff;"></i>Bugünkü Randevular</h5>
        </div>
        <div class="card-body p-0">
            <?php if (count($appointments) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="px-4 py-3">Saat</th>
                                <th class="py-3">Hasta Yakını</th>
                                <th class="py-3">Evcil Hayvan</th>
                                <th class="py-3">Şikayeti</th>
                                <th class="px-4 py-3">Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $app): ?>
                                <tr>
                                    <td class="px-4 py-3 fw-bold text-dark">
                                        <?= substr($app['appointment_time'], 0, 5) ?>
                                    </td>
                                    <td><?= htmlspecialchars($app['owner_name']) ?></td>
                                    <td class="text-primary fw-semibold"><?= htmlspecialchars($app['pet_name']) ?> <span class="badge bg-secondary ms-1 text-xs"><?= htmlspecialchars($app['animal_type']) ?></span></td>
                                    <td><?= htmlspecialchars($app['complaint'] ?? '-') ?></td>
                                    <td class="px-4">
                                        <?php
                                            // Duruma göre dinamik renk belirleme
                                            $badge = match($app['status']) {
                                                'bekliyor' => 'bg-warning-subtle text-warning',
                                                'muayenede' => 'bg-info-subtle text-info',
                                                'tedavi ediliyor' => 'bg-primary-subtle text-primary',
                                                'taburcu' => 'bg-success-subtle text-success',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                        ?>
                                        <span class="badge rounded-pill <?= $badge ?> px-3 py-2 border">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa-solid fa-face-smile-beam fs-1 mb-3" style="color:#00e6a8;"></i>
                    <p class="m-0 fs-5">Bugün için planlanmış bir randevu bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
