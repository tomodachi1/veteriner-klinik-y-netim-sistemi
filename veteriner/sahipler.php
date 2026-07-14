<?php
require_once 'db.php';

// Arama filtresi
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$params = [];

// Hasta sahiplerini gruplayarak çekiyoruz
$sql = "SELECT 
            owner_name, 
            phone, 
            COUNT(DISTINCT id) as total_appointments,
            COUNT(DISTINCT pet_name) as pet_count,
            GROUP_CONCAT(DISTINCT pet_name SEPARATOR ', ') as pet_names
        FROM patients";

if ($search !== '') {
    $sql .= " WHERE owner_name LIKE ?";
    $params[] = "%$search%";
}

$sql .= " GROUP BY owner_name, phone ORDER BY owner_name ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$owners = $stmt->fetchAll();

include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold"><i class="fa-solid fa-users me-2" style="color:#00c2ff;"></i>Hasta Sahipleri</h4>
</div>

<!-- Arama Formu -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="sahipler.php" class="row g-3">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Sahip adı ile ara..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Ara</button>
            </div>
        </form>
    </div>
</div>

<!-- Kartlar -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (count($owners) > 0): ?>
        <?php
        $avatar_gradients = [
            'linear-gradient(135deg,#ff2ec4,#d6169f)',
            'linear-gradient(135deg,#00c2ff,#0091c2)',
            'linear-gradient(135deg,#00e6a8,#049e73)',
        ];
        $i = 0;
        foreach ($owners as $owner):
            $grad = $avatar_gradients[$i % 3];
            $i++;
        ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px; background: <?= $grad ?>;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <h5 class="card-title m-0 fw-bold"><?= htmlspecialchars($owner['owner_name']) ?></h5>
                                <small class="text-muted"><i class="fa-solid fa-id-card me-1"></i>Müşteri</small>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled mb-3">
                            <li class="mb-2"><i class="fa-solid fa-phone me-2" style="color:#00e6a8;"></i> <?= htmlspecialchars($owner['phone']) ?></li>
                            <li class="mb-2"><i class="fa-solid fa-envelope text-warning me-2"></i> <span class="text-muted fst-italic">Belirtilmemiş</span></li>
                        </ul>

                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark"><i class="fa-solid fa-paw me-2" style="color:#ff2ec4;"></i>Hayvan Sayısı:</span>
                                <span class="badge bg-primary rounded-pill"><?= $owner['pet_count'] ?></span>
                            </div>
                            <small class="text-muted d-block mt-2"><strong>Evcil Hayvanlar:</strong> <?= htmlspecialchars($owner['pet_names']) ?></small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 pb-3 text-center">
                        <a href="listele.php?search=<?= urlencode($owner['owner_name']) ?>" class="btn btn-sm btn-outline-primary w-100"><i class="fa-solid fa-clock-rotate-left me-1"></i> Geçmiş Randevular</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center shadow-sm">
                Arama kriterlerinize uygun hasta sahibi bulunamadı.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
