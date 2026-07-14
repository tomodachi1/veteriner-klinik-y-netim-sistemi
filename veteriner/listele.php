<?php
require_once 'db.php';

// Filtre ve Arama Değişkenleri
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type_filter']) ? trim($_GET['type_filter']) : '';

// Sorgu İnşa Etme
$sql = "SELECT p.*, d.full_name as doctor_name FROM patients p LEFT JOIN doctors d ON p.doctor_id = d.id WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND owner_name LIKE ?";
    $params[] = "%$search%";
}

if ($type_filter !== '') {
    $sql .= " AND animal_type = ?";
    $params[] = $type_filter;
}

$sql .= " ORDER BY appointment_date DESC, appointment_time DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll();

// Filtreleme için mevcut tüm hayvan türlerini getirme
$types_query = $db->query("SELECT DISTINCT animal_type FROM patients");
$animal_types = $types_query->fetchAll(PDO::FETCH_COLUMN);

include 'header.php';
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-trash me-2"></i> Randevu kaydı başarıyla silindi.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-double me-2"></i> Randevu kaydı başarıyla güncellendi!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold"><i class="fa-solid fa-calendar-days me-2" style="color:#00c2ff;"></i>Randevular</h4>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="listele.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Sahip Adına Göre Ara</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Sahibinin adı..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Hayvan Türüne Göre Filtrele</label>
                <select name="type_filter" class="form-select">
                    <option value="">Tümü</option>
                    <?php foreach ($animal_types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $type_filter === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Filtrele</button>
                    <a href="listele.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i> Sıfırla</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold"><i class="fa-solid fa-calendar-days me-2" style="color:#00c2ff;"></i>Randevular</h5>
        <a href="ekle.php" class="btn btn-sm btn-success shadow-sm"><i class="fa-solid fa-plus me-1"></i> Yeni Randevu Ekle</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <td>
    <?php if (!empty($row['doctor_name'])): ?>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
            <i class="fa-solid fa-user-doctor me-1"></i> <?= htmlspecialchars($row['doctor_name']) ?>
        </span>
    <?php else: ?>
        <span class="text-muted" style="font-size: 12px;">Atanmadı</span>
    <?php endif; ?>
</td>
                    <tr>
                        <th>Hayvan Adı</th>
                        <th>Türü</th>
                        <th>Sahibi</th>
                        <th>Telefon</th>
                        <th>Randevu Tarihi/Saati</th>
                        <th>Ücret</th>
                        <th>Durum</th>
                        <th class="text-center">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($patients) > 0):
                        foreach ($patients as $row): 
                            $badge = match($row['status']) {
                                'bekliyor' => 'bg-warning text-dark',
                                'muayenede' => 'bg-info text-white',
                                'tedavi ediliyor' => 'bg-primary text-white',
                                'taburcu' => 'bg-success text-white',
                                default => 'bg-secondary'
                            };
                    ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row['pet_name']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['animal_type']) ?></span></td>
                            <td><?= htmlspecialchars($row['owner_name']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td>
                                <i class="fa-solid fa-calendar-alt text-muted me-1"></i> <?= date('d.m.Y', strtotime($row['appointment_date'])) ?> 
                                <span class="ms-2"><i class="fa-solid fa-clock text-muted me-1"></i> <?= substr($row['appointment_time'], 0, 5) ?></span>
                            </td>
                            <td class="fw-semibold text-end text-md-start"><?= number_format($row['fee'], 2, ',', '.') ?> ₺</td>
                            <td><span class="badge <?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="guncelle.php?id=<?= $row['id'] ?>" class="btn btn-warning" title="Düzenle"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="fis.php?id=<?= $row['id'] ?>" class="btn btn-info py-2 px-3 border-0 rounded-0" title="Fiş / Fatura Kes" style="background-color: var(--neon-cyan); color: #000;"><i class="fa-solid fa-file-invoice-dollar"></i></a>
                                    <a href="sil.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Bu kaydı silmek istediğinizden emin misiniz?')" title="Sil"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Arama kriterlerine uygun kayıt bulunamadı.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
