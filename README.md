# 🐾 PatiCare - Veteriner Klinik Yönetim Sistemi

PatiCare, küçük dostlarımızın sağlık süreçlerini, aşı takvimlerini, muayene kayıtlarını ve randevularını kolayca yönetebilmeniz için tasarlanmış, modern ve kullanıcı dostu bir **Veteriner Klinik Yönetim Sistemi**'dir.

---

## ✨ Özellikler

- **📊 Gelişmiş Dashboard (Yönetim Paneli)**: Klinik genelindeki toplam kayıtlı hasta sayısını, bugünün randevu trafiğini ve bekleyen muayeneleri anlık olarak takip edin.
- **👤 Hasta Sahipleri Yönetimi**: Hasta sahiplerini arayın, iletişim bilgilerini görün ve sahiplendikleri evcil hayvanları listeleyin.
- **🐾 Evcil Hayvan Yönetimi**: Klinik bünyesindeki tüm evcil hayvanların yaş, kilo, cinsiyet, tür ve ırk bilgilerini kaydedip güncelleyin.
- **📅 Randevu & Hekim Planlama**: Randevu çakışmalarını otomatik engelleyen akıllı randevu sistemiyle hekim ataması gerçekleştirin.
- **🩺 Muayene & Tanı Takibi**: Şikayet, tanı, tedavi, ateş ve kilo takibi içeren detaylı muayene geçmişi tutun.
- **💉 Dijital Aşı Kartı (Aşı Pasaportu)**: Evcil hayvanların aşı geçmişini kronolojik olarak izleyin, yaklaşan aşı dozlarını kaçırmayın.
- **🧑‍⚕️ Klinik Hekim Yönetimi**: Klinik hekimlerini branş ve iletişim bilgileriyle sisteme kaydedin.
- **🧾 Tahsilat Fişi / PDF**: Muayene ücretleri için KDV dahil ara toplam hesaplayan, doğrudan yazdırılabilir tahsilat fişi oluşturun.

---

## 🛠️ Kullanılan Teknolojiler

- **Backend**: PHP 8.x (PDO / MySQL)
- **Frontend**: Bootstrap 5, Vanilla CSS, FontAwesome 6, HTML5
- **Tasarım**: Modern Neon / Light Design System (Manrope Fontu)
- **Veritabanı**: MySQL

---

## ⚙️ Kurulum ve Çalıştırma

### Gereksinimler
- PHP 8.0 veya üzeri
- MySQL / MariaDB veritabanı sunucusu
- Lokal web sunucusu (Laragon, XAMPP, WampServer vb.)

### 1. Projeyi Klonlayın
```bash
git clone https://github.com/kullanici_adiniz/paticare.git
```
Klonladığınız klasörü web sunucunuzun kök dizinine (`htdocs`, `www` veya `public_html`) taşıyın.

### 2. Veritabanını Oluşturun
MySQL yöneticinizde (phpMyAdmin, HeidiSQL veya MySQL CLI) `veteriner_db` adında yeni bir veritabanı oluşturun ve karakter setini `utf8mb4_general_ci` olarak ayarlayın.

Ardından aşağıdaki tabloları veritabanınıza aktarın:

```sql
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `specialty` varchar(255) DEFAULT 'Veteriner Hekim',
  `phone` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `patients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pet_name` varchar(100) NOT NULL,
  `animal_type` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `owner_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `complaint` text,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('bekliyor','muayenede','tedavi ediliyor','taburcu') DEFAULT 'bekliyor',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `age` varchar(50) DEFAULT 'Bilinmiyor',
  `weight` varchar(50) DEFAULT 'Bilinmiyor',
  `gender` varchar(20) DEFAULT 'Bilinmiyor',
  `is_hidden` tinyint(1) DEFAULT '0',
  `diagnosis` text,
  `treatment` text,
  `temperature` varchar(20) DEFAULT 'Bilinmiyor',
  `vaccine_name` varchar(255) DEFAULT NULL,
  `next_dose_date` date DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Veritabanı Bağlantısını Yapılandırın
`db.php` dosyasını açarak MySQL bağlantı ayarlarınızı girin:
```php
<?php
$host = 'localhost';
$dbname = 'veteriner_db';
$username = 'root'; // MySQL Kullanıcı Adınız
$password = '';     // MySQL Şifreniz
```

### 4. Tarayıcıda Açın
Web sunucunuzu çalıştırın ve tarayıcınızdan projeye gidin:
```
http://localhost/veteriner
```

---

## 📁 Proje Yapısı

```
veteriner/
├── db.php             # Veritabanı bağlantı yapılandırması
├── header.php         # Sayfa üst şablonu ve CSS tasarımları
├── footer.php         # Sayfa alt şablonu ve Bootstrap JS
├── index.php          # Yönetim Paneli (Dashboard)
├── sahipler.php       # Hasta Sahipleri listesi
├── hayvanlar.php      # Kayıtlı Hayvanlar (Kart görünümlü listeleme)
├── listele.php        # Randevular sayfası
├── muayneler.php      # Muayeneler ve Klinik Tanı geçmişi
├── asi_karti.php      # Dijital Aşı Karnesi
├── doktorlar.php      # Hekim Yönetim sayfası
├── ekle.php           # Yeni randevu ve hasta kayıt formu
├── guncelle.php       # Hasta ve randevu bilgi güncelleme ekranı
├── fis.php            # Yazdırılabilir Tahsilat Fişi
└── sil.php            # Kayıt silme işlemleri
```

---

## 🤝 Katkıda Bulunma
1. Projeyi Fork'layın (`fork`)
2. Yeni bir Feature Branch oluşturun (`git checkout -b feature/harikaOzellik`)
3. Değişikliklerinizi commit edin (`git commit -m 'Yeni özellik eklendi'`)
4. Branch'inizi push edin (`git push origin feature/harikaOzellik`)
5. Bir Pull Request açın

---

## 📝 Lisans
Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.
