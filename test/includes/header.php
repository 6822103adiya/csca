<?php
require_once __DIR__ . '/../config/functions.php';
$siteName = getSetting('site_name', 'Даам Тэтгэлэг');
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= $siteName ?></title>
    <meta name="description" content="<?= getSetting('site_description') ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/" class="logo">
                <div class="logo-icon">🎓</div>
                Даам <span>Тэтгэлэг</span>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="nav-desktop">
                <a href="/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Нүүр</a>
                <a href="/about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">Бидний тухай</a>
                <a href="/scholarship.php" class="<?= $currentPage === 'scholarship' ? 'active' : '' ?>">Тэтгэлэг</a>
                <a href="/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Холбоо барих</a>
                <a href="/register.php" class="btn btn-primary">Бүртгүүлэх</a>
            </nav>
            
            <!-- Mobile Hamburger Button -->
            <button class="hamburger" id="hamburger" type="button">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
        
        <!-- Mobile Dropdown Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Нүүр</a>
            <a href="/about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">Бидний тухай</a>
            <a href="/scholarship.php" class="<?= $currentPage === 'scholarship' ? 'active' : '' ?>">Тэтгэлэг</a>
            <a href="/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Холбоо барих</a>
        </div>
    </header>
    
    <main>
