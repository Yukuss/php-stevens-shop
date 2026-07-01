<?php
session_start();
include_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $errors = [];
    if (empty($login)) $errors[] = 'Введите логин';
    if (empty($password)) $errors[] = 'Введите пароль';

    if (empty($errors)) {
        $hashedPassword = md5($password);

        $query = "SELECT id, login FROM users WHERE login = '$login' AND pass_hash = '$hashedPassword'";
        $result = mysqli_query($link, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Неверный логин или пароль';
        }
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////
    if (!empty($errors)) {
        echo '<script>alert("';
        foreach ($errors as $error) {
            echo $error."\\n";
        }
        echo '");</script>';
    
        echo '<script>history.back();</script>';
        exit;
    }
}
?>