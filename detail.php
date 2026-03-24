<?php
include("db.php");
session_start();

// ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Ürün bilgilerini ve ilanı açan kişinin mailini çekmek için LEFT JOIN ekledik
$query = "SELECT items.*, categories.category_name, users.email AS owner_email 
          FROM items 
          JOIN categories ON items.category_id = categories.category_id 
          LEFT JOIN users ON items.user_id = users.user_id 
          WHERE items.item_id = $id";

$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    die("İlan bulunamadı.");
}

// Navbar için kullanıcı adını çekelim
$user_name = "";
if (isset($_SESSION["user_id"])) {
    $u_id = $_SESSION["user_id"];
    $user_query = mysqli_query($conn, "SELECT full_name FROM users WHERE user_id = '$u_id'");
    if ($u_row = mysqli_fetch_assoc($user_query)) {
        $user_name = $u_row['full_name'];
    }
}

$status_label = ($item['status'] == 'Lost') ? 'Kayıp Eşya' : 'Bulunan Eşya';
$status_class = strtolower($item['status']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['title']) ?> | FBU CAMPUS FINDER</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --fb-navy: #00235d;
            --fb-yellow: #fed100;
            --fb-yellow-dark: #e6bc00;
            --bg: #f4f6f9;
            --white: #ffffff;
            --text-dark: #001538;
            --text-light: #64748b;
            --border: #e2e8f0;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR */
        .navbar {
            background: var(--fb-navy);
            padding: 0.8rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 15px;
        }

        .logo-img { height: 50px; width: auto; }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--fb-yellow);
            letter-spacing: 1px;
            border-left: 2px solid var(--fb-yellow);
            padding-left: 15px;
            line-height: 1;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info {
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .user-info i { color: var(--fb-yellow); font-size: 1.2rem; }

        .btn-auth {
            text-decoration: none;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        .btn-logout { background: #ef4444; color: white; }

        /* Ana İçerik */
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        .detail-card {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            border-bottom: 6px solid var(--fb-yellow);
        }

        .status-banner {
            padding: 15px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        .status-banner.lost { background: #fee2e2; color: #ef4444; }
        .status-banner.found { background: #dcfce7; color: #10b981; }

        .content-padding { padding: 40px; }

        .category-tag {
            display: inline-block;
            background: #f1f5f9;
            color: var(--fb-navy);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 2.25rem;
            margin: 0 0 20px 0;
            font-weight: 800;
            color: var(--fb-navy);
        }

        .description-text {
            color: var(--text-dark);
            font-size: 1.1rem;
            margin-bottom: 30px;
            white-space: pre-wrap;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid var(--fb-yellow);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
        }

        .info-item { display: flex; flex-direction: column; gap: 5px; }

        .info-label {
            font-size: 0.85rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .info-value {
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--fb-navy);
        }

        .action-area {
            background: #f9fafb;
            padding: 30px 40px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover { color: var(--fb-navy); }

        .btn-contact {
            background: var(--fb-navy);
            color: var(--fb-yellow);
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            box-shadow: 0 4px 0px #000c20;
        }

        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 35, 93, 0.2);
        }

        footer {
            background: var(--fb-navy);
            color: var(--white);
            padding: 30px 5%;
            text-align: center;
            border-top: 5px solid var(--fb-yellow);
            margin-top: auto;
        }

        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            h1 { font-size: 1.75rem; }
            .action-area { flex-direction: column; gap: 20px; }
            .logo-text { display: none; }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <nav class="navbar">
        <a href="index.php" class="logo-container">
            <img src="images/fbu_logo.png" alt="FBU Logo" class="logo-img">
            <span class="logo-text">CAMPUS<br>FINDER</span>
        </a>

        <div class="nav-links">
            <?php if (isset($_SESSION["user_id"])): ?>
                <div class="user-info">
                    <i class="fa-solid fa-circle-user"></i>
                    <span><?php echo htmlspecialchars($user_name); ?></span>
                </div>
                <a href="logout.php" class="btn-auth btn-logout">Çıkış Yap</a>
            <?php else: ?>
                <a href="login.php" class="btn-auth" style="background: var(--fb-yellow); color: var(--fb-navy);">Giriş Yap</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="detail-card">
            <div class="status-banner <?= $status_class ?>">
                <i class="fa-solid <?= ($item['status'] == 'Lost') ? 'fa-circle-question' : 'fa-circle-check' ?>"></i> 
                <?= $status_label ?>
            </div>

            <div class="content-padding">
                <span class="category-tag"><?= htmlspecialchars($item['category_name']) ?></span>
                <h1><?= htmlspecialchars($item['title']) ?></h1>
                
                <div class="description-text">
                    <?= !empty($item['description']) ? nl2br(htmlspecialchars($item['description'])) : "Bu ilan için detaylı bir açıklama girilmemiş." ?>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Konum / Yer</span>
                        <span class="info-value">
                            <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i>
                            <?= htmlspecialchars($item['location']) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">İlan Sahibi (Mail)</span>
                        <span class="info-value">
                            <i class="fa-solid fa-envelope" style="color: var(--fb-navy);"></i>
                            <?= htmlspecialchars($item['owner_email'] ?? 'Belirtilmemiş') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="action-area">
                <a href="index.php" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Listeye Dön
                </a>
                
                <?php if (!empty($item['owner_email'])): ?>
                <a href="mailto:<?= $item['owner_email'] ?>?subject=FBÜ CampusFinder İlanı Hakkında" class="btn-contact">
                    <i class="fa-solid fa-comment-dots"></i> İletişime Geç
                </a>
                <?php else: ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 FBU | CAMPUS FINDER - Fenerbahçe Üniversitesi Öğrenci Portalı</p>
</footer>

</body>
</html>