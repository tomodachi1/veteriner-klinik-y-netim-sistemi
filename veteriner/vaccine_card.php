<?php
// vaccine_card.php
require_once 'db.php'; // Veritabanı bağlantısı ($db değişkenini içermelidir)

// 1. Seçim şeridinde listelemek için tüm hayvanları çekelim (Değişken ismi $db olarak güncellendi)
$pets = $db->query("SELECT * FROM pets")->fetchAll();

// 2. Seçilen bir hayvan var mı kontrol edelim (Yoksa ilk hayvanı varsayılan seçelim)
$selected_pet_id = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : ($pets[0]['id'] ?? 0);

$vaccines = [];
$selected_pet = null;

if ($selected_pet_id > 0) {
    // Seçili hayvanın ve sahibinin bilgilerini birleştirerek getirelim
    $stmt = $db->prepare("
        SELECT p.*, o.name_surname 
        FROM pets p 
        JOIN owners o ON p.owner_id = o.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$selected_pet_id]);
    $selected_pet = $stmt->fetch();

    // Bu hayvana ait tüm aşı geçmişini kronolojik olarak getirelim
    $stmt_vac = $db->prepare("
        SELECT * FROM vaccinations 
        WHERE pet_id = ? 
        ORDER BY application_date DESC
    ");
    $stmt_vac->execute([$selected_pet_id]);
    $vaccines = $stmt_vac->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetKlinik - Dijital Aşı Karnesi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{ font-family:'Manrope', system-ui, sans-serif; }
        .aurora-bg{
            background:
                radial-gradient(650px 400px at 5% -10%, rgba(255,46,196,.14), transparent 60%),
                radial-gradient(600px 400px at 100% 0%, rgba(0,194,255,.14), transparent 60%),
                radial-gradient(650px 450px at 50% 115%, rgba(0,230,168,.13), transparent 60%),
                #f5f6fb;
        }
        ::-webkit-scrollbar{ width:8px; height:8px; }
        ::-webkit-scrollbar-thumb{ background: linear-gradient(#ff2ec4, #00c2ff); border-radius: 10px; }
    </style>
</head>
<body class="aurora-bg font-sans antialiased text-slate-800">

    <div class="flex h-screen overflow-hidden">
        <div class="w-64 bg-white/80 backdrop-blur-md border-r border-slate-100 flex flex-col justify-between flex-shrink-0 print:hidden">
            <div class="p-6">
                <div class="flex items-center space-x-2">
                    <div class="text-white p-2 rounded-xl font-bold text-sm shadow-lg" style="background:linear-gradient(135deg,#ff2ec4,#00c2ff); box-shadow:0 8px 20px -6px rgba(255,46,196,.5);">
                        🐾
                    </div>
                    <span class="text-xl font-bold" style="background:linear-gradient(90deg,#ff2ec4,#00c2ff 65%,#00e6a8); -webkit-background-clip:text; background-clip:text; color:transparent;">VetKlinik</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold text-white" style="background:linear-gradient(90deg,#00c2ff,#00e6a8);">DEMO</span>
                </div>
                
                <nav class="mt-8 space-y-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3 px-4">Modüller</p>
                    <a href="index.php" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>📊</span> <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>👤</span> <span>Hasta Sahipleri</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>🐾</span> <span>Hayvanlar</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>📅</span> <span>Randevular</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>🩺</span> <span>Muayeneler</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium text-sm">
                        <span>💉</span> <span>Aşılar</span>
                    </a>
                    <a href="vaccine_card.php" class="flex items-center space-x-3 py-2.5 px-4 rounded-lg text-white font-semibold text-sm shadow-sm" style="background:linear-gradient(90deg,#ff2ec4,#00c2ff);">
                        <span>📇</span> <span>Aşı Kartı</span>
                    </a>
                </nav>
            </div>
            <div class="p-6 border-t border-slate-50 print:hidden">
                <a href="#" class="text-xs font-semibold py-2 px-4 rounded-lg block text-center transition" style="color:#049e73; background:#dcfcf1;">
                    Gerçek hesap iste →
                </a>
            </div>
        </div>

        <div class="flex-1 flex flex-col overflow-y-auto">
            <header class="bg-white/70 backdrop-blur-md border-b border-slate-100 py-4 px-8 flex justify-between items-center print:hidden">
                <div class="flex items-center space-x-2">
                    <span class="text-slate-400">← Ana sayfa</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-600 font-medium">Aşı Kartı</span>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                        📥 PDF İndir
                    </button>
                    <button onclick="window.print()" class="text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2" style="background:linear-gradient(90deg,#00c2ff,#00e6a8);">
                        🖨️ Yazdır
                    </button>
                </div>
            </header>

            <main class="p-8 space-y-6">
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 print:hidden">
                    <?php foreach ($pets as $p): ?>
                        <a href="?pet_id=<?= $p['id'] ?>" 
                           class="p-4 bg-white rounded-xl border text-center transition duration-150 block
                           <?= $p['id'] == $selected_pet_id 
                               ? 'border-transparent ring-2 shadow-md' 
                               : 'border-slate-100 hover:border-slate-200' ?>"
                           <?= $p['id'] == $selected_pet_id ? 'style="box-shadow:0 8px 20px -8px rgba(255,46,196,.35); --tw-ring-color:#ff2ec4;"' : '' ?>>
                            <div class="h-10 w-10 mx-auto rounded-full flex items-center justify-center font-bold text-white mb-2" style="background:linear-gradient(135deg,#ff2ec4,#00c2ff);">
                                🐾
                            </div>
                            <span class="block font-bold text-slate-800 text-sm"><?= htmlspecialchars($p['name']) ?></span>
                            <span class="text-xs text-slate-400 mt-0.5 block"><?= htmlspecialchars($p['breed']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($selected_pet): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden print:shadow-none print:border-none">
                    
                    <div class="text-white p-8 relative overflow-hidden" style="background:linear-gradient(135deg,#10101f,#1b0f2e 55%,#0d1a2b);">
                        <div class="absolute left-0 right-0 bottom-0 h-[3px]" style="background:linear-gradient(90deg,#ff2ec4,#00c2ff,#00e6a8);"></div>
                        <div class="flex justify-between items-end">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded text-white" style="background:linear-gradient(90deg,#ff2ec4,#00c2ff);">
                                    Aşı Kartı
                                </span>
                                <h3 class="text-3xl font-extrabold text-white mt-4 mb-1 tracking-tight">
                                    <?= htmlspecialchars($selected_pet['name']) ?>
                                </h3>
                                <p class="text-slate-400 text-sm font-medium">
                                    <?= htmlspecialchars($selected_pet['breed']) ?> • <?= htmlspecialchars($selected_pet['age']) ?> yaş • <?= htmlspecialchars($selected_pet['gender']) ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-500 uppercase font-semibold tracking-wider block mb-1">Sahibi</span>
                                <span class="text-lg font-bold text-white block">
                                    <?= htmlspecialchars($selected_pet['name_surname']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h4 class="text-sm font-bold text-slate-800 mb-6">Aşı Geçmişi</h4>
                        
                        <?php if (count($vaccines) > 0): ?>
                            <div class="space-y-4">
                                <?php foreach ($vaccines as $vac): ?>
                                    <div class="flex justify-between items-center p-5 rounded-2xl border border-slate-100 bg-white hover:border-slate-200 hover:shadow-md transition">
                                        <div class="flex items-center space-x-4">
                                            <div class="h-11 w-11 rounded-full flex items-center justify-center text-white" style="background:linear-gradient(135deg,#00e6a8,#049e73);">
                                                ✓
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 text-base"><?= htmlspecialchars($vac['vaccine_name']) ?></p>
                                                <p class="text-xs text-slate-400 mt-1">Uygulama: <?= date('Y-m-d', strtotime($vac['application_date'])) ?></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-slate-400 block font-medium">Sonraki doz</span>
                                            <span class="text-sm font-bold mt-1 block" style="color:#049e73;">
                                                <?= date('Y-m-d', strtotime($vac['next_dose_date'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-10 text-slate-400 border border-dashed border-slate-200 rounded-2xl">
                                <p class="text-sm">Bu hayvana ait henüz aşı kaydı eklenmemiş.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                    <div class="text-center py-12 text-slate-500 bg-white rounded-2xl border border-slate-100 max-w-lg mx-auto">
                        <span class="text-3xl block mb-2">⚠️</span>
                        <p class="font-semibold text-slate-700">Görüntülenecek evcil hayvan bulunamadı.</p>
                        <p class="text-xs text-slate-400 mt-1">Lütfen veritabanına hayvan ekleyin.</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

</body>
</html>
