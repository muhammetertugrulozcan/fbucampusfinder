<?php
include("db.php"); 
include("auth.php");

// Admin yetki kontrolü
if (!isAdmin()) {
    // Giriş yapmamışsa veya admin değilse index'e gönder ve hata mesajı ekle
    header("Location: index.php?alert=unauthorized");
    exit();
}
$message = "";

// --- İŞLEM MANTIĞI ---

// 1. Yeni İlan Ekleme (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_item'])) {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $location = mysqli_real_escape_string($conn, trim($_POST['location']));
    $category_id = (int)$_POST['category_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']); 

    if ($title && $location && $category_id && $status) {
        $query = "INSERT INTO items (title, location, category_id, status) VALUES ('$title', '$location', $category_id, '$status')";
        if (mysqli_query($conn, $query)) {
            $message = "İlan başarıyla eklendi.";
        }
    }
}

// 2. Durum Değiştirme (Toggle)
if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "UPDATE items SET status = IF(status='Lost', 'Found', 'Lost') WHERE item_id = $id");
    header("Location: admin.php?msg=updated");
    exit();
}

// 3. Silme İşlemi (Delete)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($conn, "DELETE FROM items WHERE item_id = $id");
    header("Location: admin.php?msg=deleted");
    exit();
}

// Verileri Çek
$sql = "SELECT items.*, categories.category_name FROM items 
        JOIN categories ON items.category_id = categories.category_id ORDER BY items.item_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli | CampusFinder</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --fb-navy: #00235d; 
            --fb-yellow: #fed100; 
            --fb-yellow-dark: #e6bc00;
            --bg: #f0f2f5; 
            --white: #ffffff; 
            --text: #001538; 
            --border: #d1d5db; 
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; color: var(--text); }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--fb-navy); color: white; height: 100vh; position: fixed; padding: 25px; box-sizing: border-box; border-right: 4px solid var(--fb-yellow); }
        .sidebar h2 { font-size: 1.25rem; margin-bottom: 30px; color: var(--fb-yellow); display: flex; align-items: center; gap: 10px; }
        .nav-link { color: #cbd5e1; text-decoration: none; display: block; padding: 12px 0; transition: 0.3s; font-weight: 500; }
        .nav-link:hover { color: var(--fb-yellow); transform: translateX(5px); }
        .nav-link i { width: 25px; }

        .main-content { margin-left: 260px; width: 100%; padding: 40px; box-sizing: border-box; }
        
        h1 { font-weight: 800; color: var(--fb-navy); margin-bottom: 30px; border-left: 5px solid var(--fb-yellow); padding-left: 15px; }

        .grid-layout { display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start; }

        /* Kart Yapısı */
        .card { background: var(--white); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0,35,93,0.08); border: 1px solid var(--border); }
        h3 { margin-top: 0; margin-bottom: 20px; font-size: 1.1rem; color: var(--fb-navy); border-bottom: 2px solid var(--fb-yellow); display: inline-block; padding-bottom: 5px; }

        /* Tablo Stil */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid var(--bg); color: #64748b; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid var(--bg); font-size: 0.9rem; }
        
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .lost { background: #fee2e2; color: #ef4444; }
        .found { background: #dcfce7; color: #10b981; }

        /* Form Stil */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 0.85rem; font-weight: 700; color: var(--fb-navy); }
        input, select { width: 100%; padding: 12px; border: 2px solid var(--border); border-radius: 10px; box-sizing: border-box; font-family: inherit; transition: 0.3s; }
        input:focus, select:focus { border-color: var(--fb-navy); outline: none; box-shadow: 0 0 0 3px rgba(0,35,93,0.1); }
        
        button { 
            width: 100%; 
            padding: 14px; 
            background: var(--fb-yellow); 
            color: var(--fb-navy); 
            border: none; 
            border-radius: 10px; 
            font-weight: 800; 
            cursor: pointer; 
            transition: 0.3s; 
            text-transform: uppercase;
            box-shadow: 0 4px 0 #d4af37;
        }
        button:hover { background: var(--fb-yellow-dark); transform: translateY(-2px); }

        .btn-icon { color: var(--fb-navy); transition: 0.2s; font-size: 1.1rem; }
        .btn-icon:hover { color: var(--fb-yellow-dark); }
        .btn-delete:hover { color: #ef4444; }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 0.9rem; background: #dcfce7; color: #15803d; border-left: 5px solid #10b981; font-weight: 600; }

        @media (max-width: 1100px) {
            .grid-layout { grid-template-columns: 1fr; }
            .sidebar { width: 80px; padding: 15px; }
            .sidebar h2 span, .nav-link span { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-star"></i> <span>Admin</span></h2>
    <a href="admin.php" class="nav-link"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a>
    <a href="index.php" class="nav-link" style="margin-top: 50px; color: #fca5a5;"><i class="fa-solid fa-right-from-bracket"></i> <span>Çıkış Yap</span></a>
</div>

<div class="main-content">
    <h1>Yönetim Paneli</h1>
    
    <?php if ($message || isset($_GET['msg'])): ?>
        <div class="alert">
            <i class="fa-solid fa-circle-check"></i> 
            <?= $message ?: ($_GET['msg'] == 'deleted' ? 'İlan silindi.' : 'Durum başarıyla güncellendi.') ?>
        </div>
    <?php endif; ?>

    <div class="grid-layout">
        <div class="card">
            <h3>Mevcut İlanlar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Eşya Detayı</th>
                        <th>Durum</th>
                        <th style="text-align: center;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($r = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--fb-navy);"><?= htmlspecialchars($r['title']) ?></div>
                            <small style="color: #64748b;"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($r['location']) ?></small>
                        </td>
                        <td><span class="badge <?= strtolower($r['status']) ?>"><?= $r['status'] == 'Lost' ? 'Kayıp' : 'Bulundu' ?></span></td>
                        <td style="text-align: center;">
                            <a href="admin.php?action=toggle&id=<?=$r['item_id']?>" class="btn-icon" title="Durumu Değiştir"><i class="fa-solid fa-sync"></i></a>
                            <a href="admin.php?action=delete&id=<?=$r['item_id']?>" class="btn-icon btn-delete" style="margin-left: 15px;" onclick="return confirm('Bu ilanı silmek istediğinize emin misiniz?')" title="Sil"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Hızlı İlan Ekle</h3>
            <form method="POST">
                <input type="hidden" name="add_item" value="1">
                <div class="form-group">
                    <label>Eşya Adı</label>
                    <input type="text" name="title" placeholder="Örn: FB Anahtarlık" required>
                </div>
                <div class="form-group">
                    <label>Konum</label>
                    <input type="text" name="location" placeholder="Örn: Kantin" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" required>
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while ($c = mysqli_fetch_assoc($cats)) {
                            echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Durum</label>
                    <select name="status" required>
                        <option value="Lost">Kayıp</option>
                        <option value="Found">Bulundu</option>
                    </select>
                </div>
                <button type="submit">İlanı Kaydet</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>