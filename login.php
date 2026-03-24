<?php
include("db.php");
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role"] = $user["role"];

            // Yönlendirme mantığı düzeltildi
            if ($user["role"] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }

    $error = "E-posta adresi veya şifre hatalı!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | FBU CAMPUS FINDER</title>
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
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--fb-navy);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            border-bottom: 6px solid var(--fb-yellow);
        }

        /* Logo Alanı Düzenlemesi */
        .logo-box {
            width: 100px;
            height: 100px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 4px solid var(--fb-yellow);
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .login-logo {
            width: 85%;
            height: auto;
            object-fit: contain;
        }

        h2 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: var(--fb-navy);
        }

        p.subtitle {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 42px;
            color: var(--text-light);
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 8px;
            padding-left: 5px;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
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
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
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
            letter-spacing: 1px;
            box-shadow: 0 4px 0px #001538;
        }

        button:hover {
            background: #001a45;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 35, 93, 0.3);
        }

        .footer-links {
            margin-top: 25px;
            font-size: 0.85rem;
            color: var(--white);
            text-align: center;
        }

        .footer-links a {
            color: var(--fb-yellow);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo-box">
            <img src="images\fbu_yazi.png" alt="FBÜ Logo" class="login-logo">
        </div>
        
        <h2>Hoş Geldin!</h2>
        <p class="subtitle">FBÜ hesabınla giriş yaparak ilanları yönet.</p>

        <?php if ($error): ?>
            <div class="error-msg">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Okul E-postası</label>
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="isim@stu.fbu.edu.tr" required>
            </div>

            <div class="form-group">
                <label>Şifre</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Giriş Yap</button>
        </form>
    </div>

    <div class="footer-links">
        Hesabın yok mu? <a href="register.php">Hemen Kayıt Ol</a><br>
        <a href="index.php" style="display:inline-block; margin-top:15px; color:white; font-weight:400;">← Ana Sayfaya Dön</a>
    </div>
</div>

</body>
</html>