<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    die('Нужно авторизоваться');
}

if (isset($_GET['remove'])) {
    $item_id = intval($_GET['remove']);
    mysqli_query($link, "DELETE FROM cart WHERE id = $item_id AND user_id = {$_SESSION['user_id']}");
}

$cart_items = mysqli_query($link, "
    SELECT c.id as cart_id, p.id, p.name, p.price, p.image, c.quantity 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = {$_SESSION['user_id']}
");
?>

<main>
    <h1>Ваша корзина</h1>
    
    <?php if (mysqli_num_rows($cart_items) > 0): ?>
        <div class="cart-items">
            <?php 
            $total = 0;
            while ($item = mysqli_fetch_assoc($cart_items)): 
                $total += $item['price'] * $item['quantity'];
            ?>
                <div class="cart-item">
                    <?php if ($item['image']): ?>
                        <img src="assets/<?= $item['image'] ?>" width="100">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p>Цена: <?= $item['price'] ?> BYN</p>
                    <p>Количество: <?= $item['quantity'] ?></p>
                    <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="btn">Удалить</a>
                </div>
            <?php endwhile; ?>
            <div class="cart-total">
                <h3>Итого: <?= $total ?> BYN</h3>
                <button class="btn" id="checkoutBtn">Оформить заказ</button>
            </div>
        </div>
    <?php else: ?>
        <p>Ваша корзина пуста</p>
    <?php endif; ?>
</main>
<div id="orderSuccessModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeOrderModal()">X</span>
        <h2>Заказ оформлен успешно!</h2>
        <p>Мы свяжемся с вами по телефону в течение получаса</p>
        <button class="btn" onclick="closeOrderModal()">OK</button>
    </div>
</div>

<script>
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        document.getElementById('orderSuccessModal').style.display = 'flex';
    });
    
    function closeOrderModal() {
        document.getElementById('orderSuccessModal').style.display = 'none';
    }
</script>

<?php include 'includes/footer.php'; ?>