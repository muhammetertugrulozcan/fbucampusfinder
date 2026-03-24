<?php
include("db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // FBU OKUL MAİLİ KONTROLÜ
    if (
        !preg_match("/@stu\.fbu\.edu\.tr$/", $email) &&
        !preg_match("/@fbu\.edu\.tr$/", $email)
    ) {
        $error = "Yalnızca @fbu.edu.tr uzantılı okul maili ile kayıt olabilirsiniz!";
    } else {
        // E-posta daha önce kayıt edilmiş mi kontrolü
        $check = mysqli_prepare($conn, "SELECT email FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        
        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Bu e-posta adresi zaten kayıtlı!";
        } else {
            // Yeni alanlar (full_name, phone) sorguya eklendi
            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, phone, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $full_name, $phone, $email, $password);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?status=success");
                exit();
            } else {
                $error = "Sistemsel bir hata oluştu, lütfen tekrar deneyin.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | FBU CAMPUS FINDER</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --fb-navy: #00235d;
            --fb-yellow: #fed100;
            --fb-yellow-dark: #e6bc00;
            --white: #ffffff;
            --text-light: #94a3b8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #003a99, var(--fb-navy));
            min-height: 100vh; /* Yükseklik içeriğe göre uzayabilsin diye min-height yaptık */
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--fb-navy);
            padding: 20px 0;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .register-card {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            border-bottom: 6px solid var(--fb-yellow);
        }

        .logo-box {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 4px solid var(--fb-yellow);
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .register-logo {
            width: 80%;
            height: auto;
            object-fit: contain;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: var(--fb-navy);
        }

        p.subtitle {
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 15px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 40px;
            color: var(--text-light);
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.8rem;
            margin-bottom: 6px;
            padding-left: 5px;
        }

        input {
            width: 100%;
            padding: 11px 15px 11px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: 0.3s;
            outline: none;
        }

        input:focus {
            border-color: var(--fb-navy);
            box-shadow: 0 0 0 4px rgba(0, 35, 93, 0.1);
        }

        .error-msg {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--fb-navy);
            color: var(--fb-yellow);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            text-transform: uppercase;
            box-shadow: 0 4px 0px #001538;
        }

        button:hover {
            background: #001a45;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 35, 93, 0.3);
        }

        .footer-links {
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--white);
            text-align: center;
        }

        .footer-links a {
            color: var(--fb-yellow);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <div class="logo-box">
            <img src="images/fbu_yazi.png" alt="FBÜ Logo" class="register-logo">
        </div>

        <h2>Aramıza Katıl</h2>
        <p class="subtitle">Üye ol ve kampüsü keşfetmeye başla.</p>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Ad Soyad</label>
                <i class="fa-solid fa-user"></i>
                <input type="text" name="full_name" placeholder="Adınız ve Soyadınız" required>
            </div>

            <div class="form-group">
                <label>Cep Telefonu</label>
                <i class="fa-solid fa-phone"></i>
                <input type="tel" name="phone" placeholder="05XX XXX XX XX" required>
            </div>

            <div class="form-group">
                <label>Okul E-postası</label>
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="örnek@stu.fbu.edu.tr" required>
            </div>

            <div class="form-group">
                <label>Şifre Oluştur</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="En az 6 karakter" minlength="6" required>
            </div>

            <button type="submit">Kayıt Ol</button>
        </form>
    </div>

    <div class="footer-links">
        Zaten bir hesabın var mı? <a href="login.php">Giriş Yap</a><br>
        <a href="index.php" style="display:inline-block; margin-top:15px; color:white; font-weight:400; text-decoration:underline;">← Ana Sayfaya Dön</a>
    </div>
</div>

</body>
</html>