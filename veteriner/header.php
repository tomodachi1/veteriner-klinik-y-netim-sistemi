<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PatiCare - Veteriner Klinik Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ======================================================
           PATICARE — NEON / LIGHT DESIGN SYSTEM
           Neon Pink · Neon Green · Neon Cyan on a light canvas
           ====================================================== */
        :root{
            --neon-pink:   #ff2ec4;
            --neon-pink-2: #d6169f;
            --neon-green:  #00e6a8;
            --neon-green-2:#049e73;
            --neon-cyan:   #00c2ff;
            --neon-cyan-2: #0091c2;

            --ink:      #12121f;
            --ink-soft: #5c5c74;
            --line:     #ecebf7;
            --surface:  #ffffff;
            --surface-2:#f6f6fd;
            --bg-base:  #f5f6fb;

            /* Bootstrap semantic remap → neon palette */
            --bs-primary: var(--neon-cyan);
            --bs-primary-rgb: 0,194,255;
            --bs-primary-bg-subtle: #e2f7ff;
            --bs-primary-border-subtle: #a9e6ff;
            --bs-primary-text-emphasis: #006e93;

            --bs-success: var(--neon-green);
            --bs-success-rgb: 0,230,168;
            --bs-success-bg-subtle: #dcfcf1;
            --bs-success-border-subtle: #9ff2d8;
            --bs-success-text-emphasis: #037a5a;

            --bs-info: #00b8d9;
            --bs-info-rgb: 0,184,217;
            --bs-info-bg-subtle: #e0f9ff;
            --bs-info-border-subtle: #9fe9fb;
            --bs-info-text-emphasis: #036e83;

            --bs-danger: #ff3d81;
            --bs-danger-rgb: 255,61,129;
            --bs-danger-bg-subtle: #ffe3ee;
            --bs-danger-border-subtle: #ffb1d1;
            --bs-danger-text-emphasis: #a3134f;

            --bs-warning: #ffb020;
            --bs-warning-rgb: 255,176,32;
            --bs-warning-bg-subtle: #fff3dc;
            --bs-warning-border-subtle: #ffdd9e;
            --bs-warning-text-emphasis: #8a5300;

            --bs-secondary: #7a7a94;
            --bs-secondary-rgb: 122,122,148;
            --bs-secondary-bg-subtle: #eeeef7;
            --bs-secondary-border-subtle: #d8d8ea;
            --bs-secondary-text-emphasis: #45455a;

            --bs-dark: #0e0e1e;
            --bs-dark-rgb: 14,14,30;
            --bs-light: var(--surface-2);
            --bs-light-rgb: 246,246,253;

            --bs-body-color: var(--ink);
            --bs-body-bg: var(--bg-base);
            --bs-border-color: var(--line);
            --bs-link-color: var(--neon-cyan-2);
            --bs-link-hover-color: var(--neon-pink-2);

            --bs-border-radius: .65rem;
            --bs-border-radius-sm: .5rem;
            --bs-border-radius-lg: 1rem;
            --bs-border-radius-xl: 1.25rem;
            --bs-border-radius-xxl: 1.75rem;
            --bs-border-radius-pill: 50rem;
        }

        *{ scrollbar-width: thin; scrollbar-color: var(--neon-cyan) transparent; }
        ::-webkit-scrollbar{ width:8px; height:8px; }
        ::-webkit-scrollbar-thumb{ background: linear-gradient(var(--neon-pink), var(--neon-cyan)); border-radius: 10px; }

        html,body{ min-height:100%; }
        body{
            font-family:'Manrope', system-ui, -apple-system, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(680px 420px at 8% -8%, rgba(255,46,196,.16), transparent 60%),
                radial-gradient(620px 420px at 95% 5%, rgba(0,194,255,.16), transparent 60%),
                radial-gradient(700px 500px at 50% 110%, rgba(0,230,168,.14), transparent 60%),
                var(--bg-base);
            background-attachment: fixed;
        }
        h1,h2,h3,h4,h5,h6{ letter-spacing:-.01em; }

        a{ text-decoration:none; }

        /* ---------- Navbar (glass, light, neon) ---------- */
        .navbar.pc-navbar{
            background: rgba(255,255,255,.72);
            backdrop-filter: blur(14px) saturate(160%);
            -webkit-backdrop-filter: blur(14px) saturate(160%);
            border-bottom: 1px solid var(--line);
            position: sticky; top:0; z-index: 1030;
            box-shadow: 0 8px 30px -18px rgba(18,18,31,.25);
        }
        .pc-navbar .navbar-brand{
            font-weight:800; font-size:1.35rem; color: var(--ink);
            display:flex; align-items:center; gap:.55rem;
        }
        .pc-navbar .navbar-brand .brand-badge{
            width:38px; height:38px; border-radius:12px; display:inline-flex;
            align-items:center; justify-content:center; color:#fff; font-size:1rem;
            background: linear-gradient(135deg, var(--neon-pink), var(--neon-cyan));
            box-shadow: 0 6px 18px -4px rgba(255,46,196,.55);
        }
        .pc-navbar .navbar-brand .brand-text{
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-cyan) 65%, var(--neon-green));
            -webkit-background-clip:text; background-clip:text; color:transparent;
        }
        .pc-navbar .nav-link{
            color: var(--ink-soft); font-weight:600; font-size:.92rem;
            padding:.5rem .95rem !important; border-radius: 50rem;
            margin:0 .1rem; position:relative; transition: all .2s ease;
        }
        .pc-navbar .nav-link i{ color: var(--neon-cyan-2); margin-right:.35rem; }
        .pc-navbar .nav-link:hover{
            color: var(--ink); background: var(--surface-2);
        }
        .pc-navbar .nav-link.active{
            color:#fff;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-cyan));
            box-shadow: 0 6px 16px -6px rgba(255,46,196,.6);
        }
        .pc-navbar .nav-link.active i{ color:#fff; }
        .navbar-toggler{ border-color: var(--line) !important; box-shadow:none !important; }

        /* ---------- Cards ---------- */
        .card{
            background: rgba(255,255,255,.9);
            border: 1px solid var(--line);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }
        .card:hover{
            box-shadow: 0 18px 40px -22px rgba(18,18,31,.35);
        }
        .card-counter{
            position:relative; overflow:hidden;
        }
        .card-counter::before{
            content:""; position:absolute; inset:0 0 auto 0; height:4px;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-cyan));
        }
        .row .col-md-4:nth-of-type(2) .card-counter::before{ background: linear-gradient(90deg, var(--neon-cyan), var(--neon-green)); }
        .row .col-md-4:nth-of-type(3) .card-counter::before{ background: linear-gradient(90deg, var(--neon-green), var(--neon-pink)); }
        .card-counter:hover{ transform: translateY(-6px); }

        .card-header{ background: rgba(255,255,255,.6); border-bottom:1px solid var(--line); }
        .card-footer{ background: transparent; }

        /* ---------- Buttons ---------- */
        .btn{ font-weight:600; border-radius: var(--bs-border-radius); transition: all .2s ease; }
        .btn:focus{ box-shadow:none; }
        .btn-primary{
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-cyan-2));
            border:none; color:#fff;
        }
        .btn-primary:hover{
            box-shadow: 0 10px 24px -8px rgba(0,194,255,.55);
            transform: translateY(-1px);
            background: linear-gradient(90deg, var(--neon-cyan), var(--neon-cyan));
        }
        .btn-success{
            background: linear-gradient(90deg, var(--neon-green), var(--neon-green-2)); border:none; color:#06281f;
        }
        .btn-success:hover{ box-shadow: 0 10px 24px -8px rgba(0,230,168,.55); transform: translateY(-1px); }
        .btn-danger{
            background: linear-gradient(90deg, var(--bs-danger), var(--bs-danger-text-emphasis)); border:none;
        }
        .btn-danger:hover{ box-shadow: 0 10px 24px -8px rgba(255,61,129,.5); transform: translateY(-1px); }
        .btn-warning{ background: linear-gradient(90deg, #ffb020, #ff8a00); border:none; color:#3a2200; }
        .btn-warning:hover{ box-shadow: 0 10px 24px -8px rgba(255,138,0,.45); transform: translateY(-1px); }
        .btn-dark{
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-pink-2)); border:none; color:#fff;
        }
        .btn-dark:hover{ box-shadow: 0 10px 24px -8px rgba(255,46,196,.55); transform: translateY(-1px); }
        .btn-secondary{ background: var(--bs-secondary); border:none; }
        .btn-outline-secondary{ border-color: var(--line); color: var(--ink-soft); }
        .btn-outline-secondary:hover{ background: var(--surface-2); color: var(--ink); border-color: var(--line); }
        .btn-outline-primary{ color: var(--neon-cyan-2); border-color: var(--neon-cyan); }
        .btn-outline-primary:hover{ background: var(--neon-cyan); border-color: var(--neon-cyan); color:#fff; }
        .btn-light{ background: var(--surface-2); border:1px solid var(--line); }
        .btn-light:hover{ background:#fff; box-shadow: 0 6px 16px -8px rgba(18,18,31,.3); }

        /* ---------- Forms ---------- */
        .form-control, .form-select{
            border-color: var(--line); background: var(--surface-2);
            border-radius: .6rem;
        }
        .form-control:focus, .form-select:focus{
            border-color: var(--neon-cyan);
            box-shadow: 0 0 0 .2rem rgba(0,194,255,.18);
            background:#fff;
        }
        .input-group-text{ background: var(--surface-2); border-color: var(--line); }
        .form-label{ color: var(--ink); font-size:.9rem; }

        /* ---------- Tables ---------- */
        .table thead.table-light th, thead.table-light{
            --bs-table-bg: var(--surface-2);
            color: var(--ink-soft); text-transform:uppercase; font-size:.72rem; letter-spacing:.06em;
            border-bottom:1px solid var(--line);
        }
        .table > :not(caption) > * > *{ border-bottom-color: var(--line); }
        .table-hover > tbody > tr:hover > *{ background-color: rgba(0,194,255,.06); }

        /* ---------- Badges / Alerts ---------- */
        .badge{ font-weight:700; letter-spacing:.01em; }
        .alert{ border-radius: 1rem; border:1px solid transparent; }
        .alert-success{ box-shadow: inset 0 0 0 1px var(--bs-success-border-subtle); }
        .alert-danger{ box-shadow: inset 0 0 0 1px var(--bs-danger-border-subtle); }
        .alert-warning{ box-shadow: inset 0 0 0 1px var(--bs-warning-border-subtle); }
        .alert-info{ box-shadow: inset 0 0 0 1px var(--bs-info-border-subtle); }

        .bg-dark{
            background: linear-gradient(135deg, #10101f, #1b0f2e 55%, #0d1a2b) !important;
            position: relative;
        }
        .bg-dark::after{
            content:""; position:absolute; left:0; right:0; bottom:0; height:3px;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-cyan), var(--neon-green));
        }

        .bg-light{ background: var(--surface-2) !important; }

        .footer{ background: rgba(255,255,255,.75) !important; backdrop-filter: blur(10px); }

        .hover-shadow:hover{ box-shadow: 0 14px 30px -16px rgba(18,18,31,.3); }
        .transition{ transition: all .2s ease; }
    </style>
</head>
<body>
<?php $pc_current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg pc-navbar mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-badge"><i class="fa-solid fa-paw"></i></span>
            <span class="brand-text">PatiCare</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'index.php' ? 'active' : '' ?>" href="index.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'sahipler.php' ? 'active' : '' ?>" href="sahipler.php"><i class="fa-solid fa-users"></i> Hasta Sahipleri</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'hayvanlar.php' ? 'active' : '' ?>" href="hayvanlar.php"><i class="fa-solid fa-paw"></i> Hayvanlar</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'listele.php' ? 'active' : '' ?>" href="listele.php"><i class="fa-solid fa-calendar-check"></i> Randevular</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'muayneler.php' ? 'active' : '' ?>" href="muayneler.php"><i class="fa-solid fa-stethoscope"></i> Muayeneler</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'asilar.php' ? 'active' : '' ?>" href="asilar.php"><i class="fa-solid fa-syringe"></i> Aşılar</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'asi_karti.php' ? 'active' : '' ?>" href="asi_karti.php"><i class="fa-solid fa-address-card"></i> Aşı Kartı</a></li>
    <li class="nav-item"><a class="nav-link <?= $pc_current === 'doktorlar.php' ? 'active' : '' ?>" href="doktorlar.php"><i class="fa-solid fa-user-doctor me-1"></i> Doktorlar</a></li>
</ul>
        </div>
    </div>
</nav>
<div class="container pb-5">
