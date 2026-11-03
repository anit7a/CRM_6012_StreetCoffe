<?php
$pageTitle = $pageTitle ?? 'Dashboard';
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | CRM 6012 Street Coffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><div class="brand-mark">6012</div><div><strong>Street Coffee</strong><small>Customer Relationship</small></div></div>
    <div class="nav-label">MENU UTAMA</div>
    <a class="nav-link-custom <?= active_page('index.php') ?>" href="index.php"><span>▦</span> Dashboard</a>
    <a class="nav-link-custom <?= active_page('customers.php') ?>" href="customers.php"><span>♙</span> Data Pelanggan</a>
    <a class="nav-link-custom <?= active_page('transactions.php') ?>" href="transactions.php"><span>▤</span> Transaksi & Poin</a>
    <a class="nav-link-custom <?= active_page('complaints.php') ?>" href="complaints.php"><span>✉</span> Keluhan & Saran</a>
    <div class="nav-label">ANALISIS PELANGGAN</div>
    <a class="nav-link-custom <?= active_page('segmentation.php') ?>" href="segmentation.php"><span>◉</span> Segmentasi</a>
    <a class="nav-link-custom <?= active_page('reports.php') ?>" href="reports.php"><span>▥</span> Laporan</a>
    <a class="nav-link-custom <?= active_page('promosi.php') ?>" href="promosi.php"><span>◇</span> Promo Pelanggan Pasif</a>
    <div class="sidebar-bottom"><div class="admin-chip"><div class="avatar">A</div><div><b><?= e($_SESSION['user_name'] ?? 'Admin') ?></b><small>Administrator</small></div></div><a href="logout.php" class="logout-link">↪ Keluar</a></div>
  </aside>
  <main class="main-content">
    <div class="topbar"><button class="menu-toggle" id="menuToggle">☰</button><div><span class="eyebrow">6012 STREET COFFEE</span><h1><?= e($pageTitle) ?></h1></div><div class="topbar-date"><?= date('d M Y') ?></div></div>
    <div class="page-content"><?php show_flash(); ?>
