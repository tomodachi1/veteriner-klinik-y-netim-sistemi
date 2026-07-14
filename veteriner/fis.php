<?php
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz fiş numarası.");
}

$id = intval($_GET['id']);

// İlgili hasta ve muayene verisini çekelim
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$id]);
$fis = $stmt->fetch();

if (!$fis) {
    die("Kayıt bulunamadı.");
}

// Fiş için sahte bir KDV (Örn: %10) ve Ara Toplam hesaplaması (İsteğe bağlı)
$kdv_orani = 0.10;
$toplam_tutar = floatval($fis['fee']);
$kdv_tutari = $toplam_tutar * $kdv_orani;
$ara_toplam = $toplam_tutar - $kdv_tutari;

// Özel yazdırma stilleri için header'ı çağırmıyoruz, kendi HTML yapımızı kuruyoruz.
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiş - <?= htmlspecialchars($fis['owner_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f6f8fc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
        }
        
        .receipt-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 40px auto;
            border-top: 8px solid #ff007f; /* Neon Pembe Vurgu */
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1rem;
            border-bottom-color: #e2e8f0;
        }

        /* YAZDIRMA (PRINT) AYARLARI */
        @media print {
            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
                border-top: none;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border-bottom: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body>

<div class="container text-center mt-4 no-print d-flex justify-content-center gap-3">
    <a href="listele.php" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold"><i class="fa-solid fa-arrow-left me-2"></i> Listeye Dön</a>
    <button onclick="window.print()" class="btn px-4 py-2 text-white fw-bold rounded-pill" style="background: linear-gradient(135deg, #00f0ff 0%, #ff007f 100%); box-shadow: 0 4px 15px rgba(255, 0, 127, 0.3);">
        <i class="fa-solid fa-print me-2"></i> Fişi Yazdır / PDF İndir
    </button>
</div>

<div class="container">
    <div class="receipt-card p-4 p-md-5">
        
        <div class="row align-items-center mb-5 pb-4 print-border" style="border-bottom: 2px dashed #e2e8f0;">
            <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                <h2 class="fw-extrabold mb-1" style="color: #0f172a;"><i class="fa-solid fa-paw me-2" style="color: #00f0ff;"></i>PatiCare <span style="color: #ff007f;">Klinik</span></h2>
                <p class="text-muted mb-0" style="font-size: 14px;">Veteriner Sağlık Hizmetleri</p>
                <p class="text-muted mb-0" style="font-size: 13px;">Neon Sokak. No: 42 Merkez / Türkiye</p>
                <p class="text-muted mb-0" style="font-size: 13px;">Tel: 0 (555) 123 45 67</p>
            </div>
            <div class="col-sm-6 text-center text-sm-end">
                <h4 class="fw-bold text-uppercase" style="color: #64748b; letter-spacing: 2px;">Tahsilat Fişi</h4>
                <div class="mt-2">
                    <span class="d-block fw-semibold text-dark">Fiş No: <span class="text-muted fw-normal">#INV-<?= str_pad($fis['id'], 5, '0', STR_PAD_LEFT) ?></span></span>
                    <span class="d-block fw-semibold text-dark">Tarih: <span class="text-muted fw-normal"><?= date('d.m.Y', strtotime($fis['appointment_date'])) ?></span></span>
                    <span class="d-block fw-semibold text-dark">Saat: <span class="text-muted fw-normal"><?= substr($fis['appointment_time'], 0, 5) ?></span></span>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 1px;">SAYIN (MÜŞTERİ BİLGİLERİ)</h6>
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($fis['owner_name']) ?></h5>
                <p class="text-muted mb-0"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($fis['phone']) ?></p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 1px;">HASTA (EVCİL HAYVAN)</h6>
                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($fis['pet_name']) ?></h5>
                <p class="text-muted mb-0"><i class="fa-solid fa-dog me-1"></i> <?= htmlspecialchars($fis['animal_type']) ?> - <?= htmlspecialchars($fis['breed'] ?: 'Irk Belirtilmedi') ?></p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-borderless">
                <thead class="bg-light rounded-3">
                    <tr>
                        <th class="text-muted text-uppercase" style="font-size: 12px; border-radius: 10px 0 0 10px;">Açıklama / Hizmet</th>
                        <th class="text-center text-muted text-uppercase" style="font-size: 12px;">Miktar</th>
                        <th class="text-end text-muted text-uppercase" style="font-size: 12px; border-radius: 0 10px 10px 0;">Tutar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong class="d-block text-dark">Veteriner Hekim Muayenesi & Tedavi</strong>
                            <small class="text-muted"><?= htmlspecialchars($fis['complaint'] ?: 'Genel Kontrol') ?></small>
                        </td>
                        <td class="text-center fw-medium">1</td>
                        <td class="text-end fw-bold"><?= number_format($toplam_tutar, 2, ',', '.') ?> ₺</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mb-5">
            <div class="col-sm-6 col-md-5">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted fw-medium">Ara Toplam:</span>
                    <span class="text-dark fw-bold"><?= number_format($ara_toplam, 2, ',', '.') ?> ₺</span>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 print-border" style="border-bottom: 1px solid #e2e8f0;">
                    <span class="text-muted fw-medium">KDV (%10):</span>
                    <span class="text-dark fw-bold"><?= number_format($kdv_tutari, 2, ',', '.') ?> ₺</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-uppercase fw-extrabold text-dark" style="font-size: 18px;">Genel Toplam:</span>
                    <span class="fw-extrabold" style="font-size: 24px; color: #ff007f;"><?= number_format($toplam_tutar, 2, ',', '.') ?> ₺</span>
                </div>
                <div class="text-end mt-2">
                    <span class="badge bg-success text-uppercase px-3 py-2" style="font-size: 11px; letter-spacing: 1px;"><i class="fa-solid fa-check me-1"></i> TAHSİL EDİLDİ</span>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 pt-4 text-muted" style="border-top: 2px dashed #e2e8f0; font-size: 13px;">
            <p class="mb-1 fw-bold text-dark">Bizi tercih ettiğiniz için teşekkür ederiz.</p>
            <p class="mb-0">Küçük dostlarımızın sağlığı bizim için önemlidir. Tekrar bekleriz!</p>
        </div>

    </div>
</div>

</body>
</html>