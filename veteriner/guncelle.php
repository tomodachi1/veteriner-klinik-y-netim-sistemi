<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listele.php");
    exit;
}

$id = intval($_GET['id']);
$errors = [];
$success = false;

// Mevcut verileri çekme
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
if ($stmt === false) {
    header("Location: listele.php");
    exit;
}
$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    header("Location: listele.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_name = trim($_POST['pet_name']);
    $animal_type = trim($_POST['animal_type']);
    $breed = trim($_POST['breed']);
    $owner_name = trim($_POST['owner_name']);
    $phone = trim($_POST['phone']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $complaint = trim($_POST['complaint']);
    $fee = floatval($_POST['fee']);
    $status = $_POST['status'];
    // Yeni eklenen alanlar:
    $age = !empty(trim($_POST['age'])) ? trim($_POST['age']) : 'Bilinmiyor';
    $weight = !empty(trim($_POST['weight'])) ? trim($_POST['weight']) : 'Bilinmiyor';
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : 'Bilinmiyor';

    // 1. Telefon Boş Bırakılamaz
    if (empty($phone)) {
        $errors[] = "Telefon numarası alanı boş bırakılamaz.";
    }

    // 2. Ücret Negatif Olamaz
    if ($fee < 0) {
        $errors[] = "Muayene ücreti negatif olamaz.";
    }

    // 3. Geçmiş Tarih Kontrolü
    $today = date('Y-m-d');
    if ($appointment_date < $today) {
        $errors[] = "Geçmiş tarihli bir randevu tarihi atayamazsınız.";
    }

    // 4. Randevu Çakışma Kontrolü (Kendisi dışındaki kayıtlar kontrol edilir)
    if (empty($errors)) {
        $formatted_time = date('H:i:00', strtotime($appointment_time));
        
        $check_stmt = $db->prepare("SELECT COUNT(*) FROM patients WHERE appointment_date = ? AND appointment_time = ? AND id != ?");
        $check_stmt->execute([$appointment_date, $formatted_time, $id]);
        if ($check_stmt->fetchColumn() > 0) {
            $errors[] = "Seçilen tarih ve saatte başka bir hastanın randevusu mevcuttur.";
        }
    }

    // Güncelleme işlemi
    if (empty($errors)) {
        $update_stmt = $db->prepare("UPDATE patients SET pet_name = ?, animal_type = ?, breed = ?, owner_name = ?, phone = ?, appointment_date = ?, appointment_time = ?, complaint = ?, fee = ?, status = ?, age = ?, weight = ?, gender = ? WHERE id = ?");
        
        $result = $update_stmt->execute([
            $pet_name, $animal_type, $breed, $owner_name, $phone, 
            $appointment_date, $appointment_time, $complaint, $fee, $status,
            $age, $weight, $gender, $id
        ]);

        if ($result) {
            header("Location: listele.php?msg=updated");
            exit;
        } else {
            $errors[] = "Güncelleme sırasında teknik bir hata oluştu.";
        }
    }
}

include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header text-white py-3" style="background:linear-gradient(90deg,#00c2ff,#00e6a8); border:none;">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Hasta Bilgilerini Güncelle</h5>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="guncelle.php?id=<?= $id ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hayvan Adı</label>
                            <input type="text" name="pet_name" class="form-control" required value="<?= htmlspecialchars($patient['pet_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hayvan Türü</label>
                            <select name="animal_type" class="form-select" required>
                                <option value="Kedi" <?= $patient['animal_type'] == 'Kedi' ? 'selected' : '' ?>>Kedi</option>
                                <option value="Köpek" <?= $patient['animal_type'] == 'Köpek' ? 'selected' : '' ?>>Köpek</option>
                                <option value="Kuş" <?= $patient['animal_type'] == 'Kuş' ? 'selected' : '' ?>>Kuş</option>
                                <option value="Kemirgen" <?= $patient['animal_type'] == 'Kemirgen' ? 'selected' : '' ?>>Kemirgen</option>
                                <option value="Diğer" <?= $patient['animal_type'] == 'Diğer' ? 'selected' : '' ?>>Diğer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Irkı / Cinsi</label>
                            <input type="text" name="breed" class="form-control" value="<?= htmlspecialchars($patient['breed']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sahibinin Adı Soyadı</label>
                            <input type="text" name="owner_name" class="form-control" required value="<?= htmlspecialchars($patient['owner_name']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefon (Zorunlu)</label>
                            <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($patient['phone']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Yaş</label>
                            <input type="text" name="age" class="form-control" placeholder="Örn: 2 veya 3 Aylık" value="<?= htmlspecialchars($patient['age'] !== 'Bilinmiyor' ? $patient['age'] : '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kilo</label>
                            <input type="text" name="weight" class="form-control" placeholder="Örn: 4.5" value="<?= htmlspecialchars($patient['weight'] !== 'Bilinmiyor' ? $patient['weight'] : '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cinsiyet</label>
                            <select name="gender" class="form-select">
                                <option value="Bilinmiyor" <?= $patient['gender'] == 'Bilinmiyor' ? 'selected' : '' ?>>Bilinmiyor</option>
                                <option value="Erkek" <?= $patient['gender'] == 'Erkek' ? 'selected' : '' ?>>Erkek</option>
                                <option value="Dişi" <?= $patient['gender'] == 'Dişi' ? 'selected' : '' ?>>Dişi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Randevu Tarihi</label>
                            <input type="date" name="appointment_date" class="form-control" required value="<?= $patient['appointment_date'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Randevu Saati</label>
                            <input type="time" name="appointment_time" class="form-control" required value="<?= substr($patient['appointment_time'], 0, 5) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Şikayeti</label>
                            <textarea name="complaint" class="form-control" rows="2"><?= htmlspecialchars($patient['complaint']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Muayene Ücreti (TL)</label>
                            <input type="number" step="0.01" name="fee" class="form-control" min="0" required value="<?= $patient['fee'] ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durum</label>
                            <select name="status" class="form-select">
                                <option value="bekliyor" <?= $patient['status'] == 'bekliyor' ? 'selected' : '' ?>>Bekliyor</option>
                                <option value="muayenede" <?= $patient['status'] == 'muayenede' ? 'selected' : '' ?>>Muayenede</option>
                                <option value="tedavi ediliyor" <?= $patient['status'] == 'tedavi ediliyor' ? 'selected' : '' ?>>Tedavi Ediliyor</option>
                                <option value="taburcu" <?= $patient['status'] == 'taburcu' ? 'selected' : '' ?>>Taburcu</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <a href="listele.php" class="btn btn-secondary me-2">Vazgeç</a>
                            <button type="submit" class="btn btn-warning text-dark"><i class="fa-solid fa-floppy-disk me-1"></i> Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
