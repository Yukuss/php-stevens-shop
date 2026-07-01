<?php
session_start();
include 'includes/db_connect.php';
$menuItems = [];
$query = "SELECT title, url FROM menu";
$result = mysqli_query($link, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menuItems[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Стивенс</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap');
    </style>
    <link rel="stylesheet" href="./assets/style.css">
<link rel="stylesheet" href="./assets/mobile-768.css" media="(max-width: 768px)">
<link rel="stylesheet" href="./assets/mobile-450.css" media="(max-width: 450px)">
<link rel="stylesheet" href="./assets/mobile-420.css" media="(max-width: 420px)">
</head>
<body>
<header>
    <div class="logo"><a class="lin" href="index.php"><img src="./assets/logo.png" alt="logo"></a></div>
    <nav>
        <ul>
            <?php 
            foreach ($menuItems as $item) {
                echo '<li><a href="' . $item['url'] . '">' . $item['title'] . '</a></li>';
            }
            ?>
            <li><a class="lin" href="cart.php">Корзина</a></li>
        </ul>
    </nav>
            <?php
        if(isset($_SESSION['user_id'])) {
            echo '<span class="hide">Добро пожаловать, ' . $_SESSION['login'] . '<br></span><a class="len" href="logout.php">Выйти</a>';
        } else {
            echo '<a class="len" onclick="openAuthModal()">Войти</a>';
        }
        ?>
</header>
    <?php include 'auth_modal.php'; ?>

