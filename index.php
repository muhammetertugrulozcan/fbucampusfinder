<?php
include('db.php'); 
session_start(); // Oturumu başlattık

// Giriş yapmış kullanıcının adını çekelim
$user_name = "";
if (isset($_SESSION["user_id"])) {
    $u_id = $_SESSION["user_id"];
    $user_query = mysqli_query($conn, "SELECT full_name FROM users WHERE user_id = '$u_id'");
    if ($u_row = mysqli_fetch_assoc($user_query)) {
        $user_name = $u_row['full_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FBU | CAMPUS FINDER</title>
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
            --text-light: #5a6b8a;
            --error-red: #ef4444;
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

        /* YETKİSİZ ERİŞİM BİLDİRİMİ (SAYFA BAŞI) */
        .top-unauthorized-alert {
            background: var(--error-red);
            color: white;
            padding: 12px 5%;
            text-align: center;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            z-index: 2000;
            animation: slideDown 0.4s ease-out;
            position: relative; /* Kapatma butonu için eklendi */
        }

        /* KAPATMA BUTONU STİLİ */
        .alert-close-btn {
            position: absolute;
            right: 20px;
            cursor: pointer;
            font-size: 1.2rem;
            opacity: 0.8;
            transition: 0.2s;
        }

        .alert-close-btn:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        .main-wrapper {
            flex: 1;
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

        .logo-img {
            height: 50px;
            width: auto;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--fb-yellow);
            letter-spacing: 1px;
            border-left: 2px solid var(--fb-yellow);
            padding-left: 15px;
            line-height: 1;
        }

        /* Nav Links */
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

        .user-info i {
            color: var(--fb-yellow);
            font-size: 1.2rem;
        }

        .btn-auth {
            text-decoration: none;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        .btn-login {
            background: var(--fb-yellow);
            color: var(--fb-navy);
        }

        .btn-login:hover { background: var(--fb-yellow-dark); }

        .btn-logout {
            background: #ef4444;
            color: white;
        }

        .btn-logout:hover { background: #dc2626; }

        /* HERO SECTION */
        .hero {
            background: linear-gradient(135deg, #00235d 0%, #003a99 100%);
            color: var(--white);
            padding: 60px 5% 100px;
            text-align: center;
            border-bottom: 5px solid var(--fb-yellow);
        }

        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; color: var(--fb-yellow); }
        .hero p { font-size: 1.1rem; opacity: 0.9; max-width: 600px; margin: 0 auto 2rem; }

        /* SEARCH & ACTION BAR */
        .action-bar {
            max-width: 900px;
            margin: -40px auto 40px;
            padding: 20px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,35,93,0.15);
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }
        .search-input:focus { border-color: var(--fb-navy); }

        .btn-add {
            background: var(--fb-yellow);
            color: var(--fb-navy);
            padding: 15px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            white-space: nowrap;
            box-shadow: 0 4px 0px #d4af37;
        }

        .btn-add:hover { 
            transform: translateY(-2px); 
            background: var(--fb-yellow-dark);
            box-shadow: 0 6px 15px rgba(254, 209, 0, 0.3); 
        }

        /* GRID & CARDS */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px 60px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }

        .card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 25px -5px rgba(0,35,93,0.1);
            border-color: var(--fb-navy);
        }

        .card-content { padding: 25px; }
        .badge { position: absolute; top: 15px; right: 15px; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .lost { background: #ffeded; color: #d00000; border: 1px solid #ffcccc; }
        .found { background: #e6fffa; color: #008a6a; border: 1px solid #b2f5ea; }

        .item-category { color: var(--fb-navy); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .item-title { font-size: 1.3rem; margin: 8px 0 12px; font-weight: 700; color: var(--fb-navy); }
        .item-info { display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.9rem; margin-bottom: 6px; }
        .item-info i { color: var(--fb-navy); width: 16px; }

        .btn-detail { color: var(--fb-navy); text-decoration: none; font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .btn-detail:hover { gap: 10px; color: var(--fb-yellow-dark); }

        /* FOOTER */
        footer {
            background: var(--fb-navy);
            color: var(--white);
            padding: 30px 5%;
            text-align: center;
            border-top: 5px solid var(--fb-yellow);
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.8rem 15px; }
            .logo-text { display: none; }
            .action-bar { flex-direction: column; margin: -20px 20px 40px; }
            .search-input, .btn-add { width: 100%; box-sizing: border-box; justify-content: center; }
        }
    </style>
</head>
<body>

<?php if (isset($_GET['alert']) && $_GET['alert'] == 'unauthorized'): ?>
    <div class="top-unauthorized-alert" id="unauthorizedAlert">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i>
        <span>BU SAYFAYA ERİŞİM YETKİNİZ BULUNMAMAKTADIR! LÜTFEN YÖNETİCİ HESABIYLA GİRİŞ YAPIN.</span>
        <i class="fa-solid fa-xmark alert-close-btn" onclick="closeAlert()"></i>
    </div>
<?php endif; ?>

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
                <a href="logout.php" class="btn-auth btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Çıkış Yap
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-auth btn-login">
                    <i class="fa-solid fa-user-lock"></i> Giriş Yap
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <h1>FBU | CAMPUS FINDER</h1>
        <p>Kampüs içerisinde kaybolan eşyaları bulun, bulunanları sahiplerine ulaştırın. FBÜ topluluğu için Sarı Lacivert dayanışma!</p>
    </section>

    <div class="container">
        <div class="action-bar">
            <input type="text" class="search-input" placeholder="Anahtar kelime ile ara (Cüzdan, Kitap...)" id="searchInput">
            <a href="add_item.php" class="btn-add">
                <i class="fa-solid fa-plus-circle"></i> İLAN VER
            </a>
        </div>

        <div class="grid">
            <?php
            $sql = "SELECT items.*, categories.category_name 
                    FROM items 
                    JOIN categories ON items.category_id = categories.category_id 
                    ORDER BY items.item_id DESC";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_class = strtolower($row['status']);
                    ?>
                    <div class="card">
                        <span class="badge <?php echo $status_class; ?>">
                            <?php echo ($row['status'] == 'Lost' ? 'Kayıp' : 'Bulundu'); ?>
                        </span>
                        <div class="card-content">
                            <div class="item-category"><?php echo htmlspecialchars($row['category_name']); ?></div>
                            <h3 class="item-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            
                            <div class="item-info">
                                <i class="fa-solid fa-location-dot"></i>
                                <?php echo htmlspecialchars($row['location']); ?>
                            </div>
                            
                            <div class="item-info">
                                <i class="fa-solid fa-clock"></i>
                                Yeni İlan
                            </div>

                            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 18px 0;">
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <a href="detail.php?id=<?php echo $row['item_id']; ?>" class="btn-detail">Detayları Gör <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1/-1; text-align:center; padding: 50px; color: var(--fb-navy); font-weight: 600;'>Henüz bir ilan bulunmuyor.</p>";
            }
            ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 FBU | CAMPUS FINDER - Fenerbahçe Üniversitesi Öğrenci Portalı</p>
</footer>

<script>
    // Arama Fonksiyonu
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            let title = card.querySelector('.item-title').innerText;
            if (title.toUpperCase().indexOf(filter) > -1) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    });

    // Bildirim Kapatma Fonksiyonu
    function closeAlert() {
        const alertBox = document.getElementById('unauthorizedAlert');
        if(alertBox) {
            alertBox.style.display = 'none';
        }
    }
</script>

</body>
</html>