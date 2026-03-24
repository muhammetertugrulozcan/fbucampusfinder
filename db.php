<?php
// Veritabanı bağlantısı - Proje şartı: Relational Database [cite: 10]
$conn = mysqli_connect("localhost", "root", "", "campus_lost_found");

if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
?>