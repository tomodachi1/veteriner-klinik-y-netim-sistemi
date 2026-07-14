<?php
require_once 'db.php';

// Gizle, Göster ve Sil İşlemlerini Yakalama
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Aynı isim ve sahibe ait tüm randevu satırlarını tek seferde güncellemek için önce hayvanı buluyoruz
    $stmt = $db->prepare("SELECT pet_name, owner_name FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $current_pet = $stmt->fetch();

    if ($current_pet) {
        $pet_name = $current_pet['pet_name'];
        $owner_name = $current_pet['owner_name'];

        if ($action === 'hide') {
            $db->prepare("UPDATE patients SET is_hidden = 1 WHERE pet_name = ? AND owner_name = ?")->execute([$pet_name, $owner_name]);
        } elseif ($action === 'show') {
            $db->prepare("UPDATE patients SET is_hidden = 0 WHERE pet_name = ? AND owner_name = ?")->execute([$pet_name, $owner_name]);
        }
    }

    if ($action === 'delete') {
        $db->prepare("DELETE FROM patients WHERE id = ?")->execute([$id]);
    }
    
    header("Location: hayvanlar.php");
    exit;
}

// Kartlar artık kaybolmayacağı için tüm hayvanları tek seferde çekiyoruz
$sql = "SELECT MIN(id) as id, pet_name, animal_type, breed, owner_name, 
               MAX(age) as age, MAX(weight) as weight, MAX(gender) as gender, MAX(is_hidden) as is_hidden 
        FROM patients 
        GROUP BY pet_name, animal_type, breed, owner_name 
        ORDER BY id DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$animals = $stmt->fetchAll();

include 'header.php';
?>
<style>
    /* 1. Tatlı Giriş (Pop-in Bounce) Animasyonu */
    @keyframes bouncyPop {
        0% { opacity: 0; transform: scale(0.6) translateY(30px); }
        60% { opacity: 1; transform: scale(1.05) translateY(-5px); }
        80% { transform: scale(0.98) translateY(2px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    .row-cols-1 > .col {
        opacity: 0;
        /* cubic-bezier ile o tatlı "esneme" hissini veriyoruz */
        animation: bouncyPop 0.7s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    }

    /* Kartların arka arkaya pıtır pıtır düşmesi için gecikmeler */
    .row-cols-1 > .col:nth-child(1) { animation-delay: 0.1s; }
    .row-cols-1 > .col:nth-child(2) { animation-delay: 0.2s; }
    .row-cols-1 > .col:nth-child(3) { animation-delay: 0.3s; }
    .row-cols-1 > .col:nth-child(4) { animation-delay: 0.4s; }
    .row-cols-1 > .col:nth-child(5) { animation-delay: 0.5s; }
    .row-cols-1 > .col:nth-child(6) { animation-delay: 0.6s; }

    /* 2. Kartın Üzerine Gelince "Kuyruk Sallama" (Wiggle) Efekti */
    @keyframes happyWiggle {
        0% { transform: translateY(-8px) rotate(0deg); }
        25% { transform: translateY(-8px) rotate(-2deg); }
        50% { transform: translateY(-8px) rotate(2deg); }
        75% { transform: translateY(-8px) rotate(-1deg); }
        100% { transform: translateY(-8px) rotate(0deg); }
    }

    .card.transition-all {
        transition: box-shadow 0.3s ease, border-color 0.3s ease !important;
    }

    .card.transition-all:hover {
        /* Kart havaya kalkar ve mutlu mutlu titrer */
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        animation: happyWiggle 0.5s ease-in-out forwards;
    }

    /* 3. Aksiyon Butonları İçin Pıt Diye Büyüme (Squish) */
    .card-footer .btn {
        transition: all 0.25s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important;
    }
    
    .card-footer .btn:hover {
        transform: scale(1.2); /* Butonlar farenin altında kabarır */
        z-index: 10;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-radius: 12px; /* Köşeleri biraz daha yuvarlanır */
    }
</style>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold"><i class="fa-solid fa-paw me-2" style="color:#ff2ec4;"></i>Kayıtlı Hayvanlar</h4>
    <small class="text-muted">Kartları gizlemek veya detayları açmak için göz butonlarını kullanabilirsiniz.</small>
</div>

<div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-4">
    <?php if (count($animals) > 0): ?>
        <?php
        $icon_palette = ['#ff2ec4', '#00c2ff', '#00e6a8'];
        $i = 0;
        foreach ($animals as $animal): 
            // Hayvan türüne göre ikon belirleme
            $icon = match(mb_strtolower($animal['animal_type'])) {
                'kedi' => 'fa-cat',
                'köpek' => 'fa-dog',
                'kuş' => 'fa-crow',
                default => 'fa-paw'
            };
            $accent = $icon_palette[$i % 3];
            $i++;
            $isHidden = ($animal['is_hidden'] == 1);
        ?>
            <div class="col">
                <!-- Eğer gizliyse karta tatlı bir pastel arka plan dokusu veriyoruz -->
                <div class="card h-100 shadow-sm border-0 rounded-4 transition-all" style="<?= $isHidden ? 'background: #fdfeff; border: 1px dashed '.$accent.'33 !important;' : '' ?>">
                    
                    <?php if ($isHidden): ?>
                        <!-- ================= GİZLİ / SEVİMLİ İKON GÖRÜNÜMÜ ================= -->
                        <div class="card-body position-relative d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 285px;">
                            <!-- Büyük ve Hareketli Hayvan İkonu (fa-bounce ile zıplar) -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm" 
                                 style="width: 100px; height: 100px; font-size: 45px; background: <?= $accent ?>1a; color: <?= $accent ?>; border: 2px dashed <?= $accent ?>;">
                                <i class="fa-solid <?= $icon ?> fa-bounce"></i>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($animal['pet_name']) ?></h5>
                            <span class="badge rounded-pill" style="background-color: <?= $accent ?>; color: #fff; font-size: 11px;">
                                <?= htmlspecialchars($animal['animal_type']) ?>
                            </span>
                            <div class="text-muted small mt-2"><i class="fa-solid fa-moon me-1"></i> Bilgiler Uyuyor...</div>
                        </div>
                    <?php else: ?>
                        <!-- ================= NORMAL DETAYLI GÖRÜNÜM ================= -->
                        <div class="card-body position-relative">
                            <!-- Hayvan İkonu ve İsim -->
                            <div class="text-center mb-3">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px; font-size: 30px; background: <?= $accent ?>1a; color: <?= $accent ?>;">
                                    <i class="fa-solid <?= $icon ?>"></i>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($animal['pet_name']) ?></h5>
                                <span class="badge bg-secondary mt-1"><?= htmlspecialchars($animal['animal_type']) ?> - <?= htmlspecialchars($animal['breed'] ?: 'Irk Belirtilmedi') ?></span>
                            </div>

                            <!-- Özellikler -->
                            <div class="bg-light p-3 rounded-3 mb-3 text-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fa-solid fa-calendar-days me-1"></i> Yaş:</span>
                                    <span class="fw-semibold"><?= htmlspecialchars($animal['age'] ?: 'Bilinmiyor') ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="fa-solid fa-weight-scale me-1"></i> Kilo:</span>
                                    <span class="fw-semibold"><?= htmlspecialchars($animal['weight'] ?: 'Bilinmiyor') ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="fa-solid fa-venus-mars me-1"></i> Cinsiyet:</span>
                                    <span class="fw-semibold"><?= htmlspecialchars($animal['gender'] ?: 'Bilinmiyor') ?></span>
                                </div>
                            </div>

                            <!-- Sahibi -->
                            <div class="d-flex align-items-center border-top pt-3">
                                <i class="fa-solid fa-user text-muted me-2"></i>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Sahibi</small>
                                    <span class="fw-bold text-dark" style="font-size: 14px;"><?= htmlspecialchars($animal['owner_name']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Aksiyon Butonları -->
                    <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                        <div class="btn-group w-100 shadow-sm" role="group">
                            <a href="guncelle.php?id=<?= $animal['id'] ?>" class="btn btn-light border" title="Düzenle">
                                <i class="fa-solid fa-pen" style="color:#00c2ff;"></i>
                            </a>
                            
                            <?php if (!$isHidden): ?>
                                <!-- Aktifken basılırsa gizler (Tatlı ikona dönüştürür) -->
                                <a href="hayvanlar.php?action=hide&id=<?= $animal['id'] ?>" class="btn btn-light border" title="Detayları Gizle">
                                    <i class="fa-solid fa-eye-slash text-warning"></i>
                                </a>
                            <?php else: ?>
                                <!-- Gizliyken basılırsa normal detayları geri getirir -->
                                <a href="hayvanlar.php?action=show&id=<?= $animal['id'] ?>" class="btn btn-light border" title="Detayları Göster">
                                    <i class="fa-solid fa-eye" style="color:#00e6a8;"></i>
                                </a>
                            <?php endif; ?>

                            <a href="hayvanlar.php?action=delete&id=<?= $animal['id'] ?>" onclick="return confirm('Bu hayvanı tamamen silmek istediğinize emin misiniz?')" class="btn btn-light border" title="Sil">
                                <i class="fa-solid fa-trash" style="color:#ff3d81;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning text-center shadow-sm w-100">
                <i class="fa-solid fa-circle-exclamation me-2"></i> Hiç hayvan kaydı bulunamadı.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>