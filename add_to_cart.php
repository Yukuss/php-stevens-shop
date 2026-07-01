<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die('Нужно авторизоваться');
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'products.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    
    $check = mysqli_query($link, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
    
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($link, "UPDATE cart SET quantity = quantity + 1 
                           WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        mysqli_query($link, "INSERT INTO cart (user_id, product_id) 
                           VALUES ($user_id, $product_id)");
    }
    
    $_SESSION['cart_message'] = 'Товар добавлен в корзину!';
}

header("Location: $referer");
exit();
?>