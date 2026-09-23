<button class="burger">☰</button>
<nav class="nav-menu">
    ...
</nav>


<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hôtel Seguro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-container">
        
        <!-- Logo -->
        <div class="logo">
            <a href="../pages/accueil.php">
                <h1>Hôtel Seguro</h1>
            </a>
        </div>

        <!-- Bouton hamburger mobile -->
        <button class="burger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigation -->
        <nav class="nav-menu">
            <ul>
                <li><a href="../pages/accueil.php">Accueil</a></li>
                <li><a href="../pages/chambres.php">Chambres</a></li>
                <li><a href="../pages/services.php">Services</a></li>
                <li><a href="../pages/galeries.php">Galeries</a></li>
                <li><a href="../pages/offers.php">Offres</a></li>
                <li><a href="../pages/apropos.php">À propos</a></li>
                <li><a href="../pages/contact.php">Contact</a></li>
                <li>
                    <a href="../pages/reservation.php" class="btn-reserver">
                        Réserver
                    </a>
                </li>
            </ul>
        </nav>

    </div>
</header>