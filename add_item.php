<?php
include("db.php");
session_start();

// Eğer kullanıcı giriş yapmamışsa ilan vermesini engelleyelim
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?alert=unauthorized");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $category_id = (int)$_POST['category_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']); 
    $user_id = $_SESSION["user_id"]; // Oturumdaki kullanıcı ID'si

    if ($title && $location && $category_id && $status) {
        // SQL sorgusuna user_id eklendi
        $query = "INSERT INTO items (title, location, description, category_id, status, user_id) 
                  VALUES ('$title', '$location', '$description', $category_id, '$status', $user_id)";
        
        if (mysqli_query($conn, $query)) {
            $message = "İlan başarıyla yayına alındı! Ana sayfaya yönlendiriliyorsunuz...";
            header("refresh:2;url=index.php");
        } else {
            $message = "Bir hata oluştu, lütfen tekrar deneyin.";
        }
    }
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
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlan Ver | FBU CAMPUS FINDER</title>
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

        /* NAVBAR (Index ile aynı) */
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
        .btn-logout:hover { background: #dc2626; }

        /* Form Alanı */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .form-card {
            background: var(--white);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0,35,93,0.1);
            border-bottom: 5px solid var(--fb-yellow);
        }

        h2 {
            margin: 0 0 10px 0;
            font-size: 1.75rem;
            font-weight: 800;
            text-align: center;
            color: var(--fb-navy);
        }

        p.subtitle {
            color: var(--text-light);
            text-align: center;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--fb-navy);
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s;
            box-sizing: border-box;
            outline: none;
        }

        textarea { resize: vertical; min-height: 100px; }

        input:focus, select:focus, textarea:focus {
            border-color: var(--fb-navy);
            box-shadow: 0 0 0 4px rgba(0, 35, 93, 0.1);
        }

        button {
            width: 100%;
            padding: 16px;
            background: var(--fb-yellow);
            color: var(--fb-navy);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 0px #d4af37;
            text-transform: uppercase;
        }

        button:hover {
            background: var(--fb-yellow-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(254, 209, 0, 0.3);
        }

        .message-box {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--fb-navy);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .back-link:hover { text-decoration: underline; }

        footer {
            background: var(--fb-navy);
            color: var(--white);
            padding: 30px 5%;
            text-align: center;
            border-top: 5px solid var(--fb-yellow);
        }

        @media (max-width: 768px) {
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
            <div class="user-info">
                <i class="fa-solid fa-circle-user"></i>
                <span><?php echo htmlspecialchars($user_name); ?></span>
            </div>
            <a href="logout.php" class="btn-auth btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Çıkış Yap
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="form-card">
            <h2>Yeni İlan Oluştur</h2>
            <p class="subtitle">Kaybolan veya bulunan eşyanın detaylarını gir.</p>

            <?php if ($message): ?>
                <div class="message-box success">
                    <i class="fa-solid fa-circle-check"></i> <?= $message ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Eşya Adı</label>
                    <input type="text" name="title" placeholder="Örn: Mavi Sırt Çantası" required>
                </div>

                <div class="form-group">
                    <label>Nerede Bulundu / Kayboldu?</label>
                    <input type="text" name="location" placeholder="Örn: Kütüphane 2. Kat" required>
                </div>          

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" required>
                        <option value="" disabled selected>Kategori seçin</option>
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while ($c = mysqli_fetch_assoc($cats)) {
                            echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>İlan Türü</label>
                    <select name="status" required>
                        <option value="Lost">Kayıp Eşya Arıyorum</option>
                        <option value="Found">Eşya Buldum</option>
                    </select>
                </div>

                 <div class="form-group">
                    <label>Açıklama</label>
                    <textarea name="description" placeholder="Eşya hakkında ayırt edici detaylar girin (Marka, renk, içindekiler, bulunduysa yer bilgisi vb.)"></textarea>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i> İLAN YAYINLA
                </button>
            </form>

            <a href="index.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Vazgeç ve Ana Sayfaya Dön
            </a>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 Kampüs Kayıp Eşya Sistemi | FBÜ İçin Tasarlandı.</p>
</footer>

</body>
</html>