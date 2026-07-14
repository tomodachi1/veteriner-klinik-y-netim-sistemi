<?php
require_once 'db.php';
$errors = [];
$success = false;

// Doktorları Forma Çekelim
$doctors = $db->query("SELECT * FROM doctors ORDER BY full_name ASC")->fetchAll();

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
    // Yeni eklenen alan:
    $doctor_id = !empty($_POST['doctor_id']) ? intval($_POST['doctor_id']) : NULL;

    if (empty($phone)) $errors[] = "Telefon numarası alanı boş bırakılamaz.";
    if ($fee < 0) $errors[] = "Muayene ücreti negatif bir değer olamaz.";
    if ($appointment_date < date('Y-m-d')) $errors[] = "Geçmiş tarihli bir güne randevu oluşturamazsınız.";

    if (empty($errors)) {
        $formatted_time = date('H:i:00', strtotime($appointment_time));
        // Doktor kontrolüyle çakışmayı önleyelim
        $check_stmt = $db->prepare("SELECT COUNT(*) FROM patients WHERE appointment_date = ? AND appointment_time = ? AND doctor_id = ?");
        $check_stmt->execute([$appointment_date, $formatted_time, $doctor_id]);
        if ($check_stmt->fetchColumn() > 0 && $doctor_id != NULL) {
            $errors[] = "Seçilen doktorun bu tarih ve saatte başka bir randevusu mevcut.";
        }
    }

    if (empty($errors)) {
        $insert_stmt = $db->prepare("INSERT INTO patients (pet_name, animal_type, breed, owner_name, phone, appointment_date, appointment_time, complaint, fee, status, doctor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $insert_stmt->execute([
            $pet_name, $animal_type, $breed, $owner_name, $phone, 
            $appointment_date, $appointment_time, $complaint, $fee, $status, $doctor_id
        ]);
        if ($result) $success = true;
        else $errors[] = "Kayıt sırasında bir hata oluştu.";
    }
}

include 'header.php';
?>

<div class="row justify-content-center mb-5">
    <div class="col-md-9 col-lg-8">
        <div class="card border-0">
            <div class="card-header bg-white py-4 border-0 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-info-subtle text-primary rounded-circle mb-3" style="width: 60px; height: 60px; font-size: 24px;"><i class="fa-solid fa-plus"></i></div>
                <h4 class="m-0 fw-extrabold text-dark">Yeni Hasta ve Randevu Kaydı</h4>
            </div>
            
            <div class="card-body p-4 p-md-5 pt-0">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm"><i class="fa-solid fa-circle-check me-2"></i> Kayıt başarıyla sisteme eklendi!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm">
                        <div class="d-flex align-items-center mb-2 fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Lütfen hataları düzeltin:</div>
                        <ul class="mb-0" style="font-size: 14px;">
                            <?php foreach ($errors as $error): ?><li><?= $error ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="ekle.php" method="POST" class="mt-2">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">HAYVAN ADI</label>
                            <input type="text" name="pet_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">HAYVAN TÜRÜ</label>
                            <select name="animal_type" class="form-select" required>
                                <option value="Kedi">Kedi</option><option value="Köpek">Köpek</option><option value="Kuş">Kuş</option><option value="Diğer">Diğer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">IRKI / CİNSİ</label>
                            <input type="text" name="breed" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">SAHİBİNİN ADI</label>
                            <input type="text" name="owner_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">TELEFON</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">ATANACAK DOKTOR</label>
                            <select name="doctor_id" class="form-select">
                                <option value="">Doktor Seçilmedi</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['full_name']) ?> (<?= htmlspecialchars($doc['specialty']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">TARİH</label>
                            <input type="date" name="appointment_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">SAAT</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">ÜCRET (TL)</label>
                            <input type="number" step="0.01" name="fee" class="form-control" value="0.00" min="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">ŞİKAYETİ / NOTLAR</label>
                            <textarea name="complaint" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted fw-bold" style="font-size: 11px;">DURUM</label>
                            <select name="status" class="form-select">
                                <option value="bekliyor">Bekliyor</option><option value="muayenede">Muayenede</option><option value="tedavi ediliyor">Tedavi Ediliyor</option><option value="taburcu">Taburcu</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 text-end border-top pt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-save me-2"></i> Kaydı Tamamla</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>