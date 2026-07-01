<?php 
include 'includes/header.php';
include_once 'includes/db_connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: catalog.php");
    exit();
}

$product_id = intval($_GET['id']);

$sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: catalog.php");
    exit();
}
?>

<main class="product-simple">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
            
    <div class="product-container">
        <div class="product-image">
            <?php if (!empty($product['image'])): ?>
                <img class="proimg" src="assets/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="no-image">Нет изображения</div>
            <?php endif; ?>
        </div>
        
        <div class="product-details">

            <div class="product-description">
                <h3>Описание</h3>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            
            <div class="product-specs">
                <h3>Характеристики</h3>
                <table>
                    <tr>
                        <th>Цена</th>
                        <td><?= number_format($product['price'], 2, '.', ' ') ?> BYN</td>
                    </tr>
                    <tr>
                        <th>Наличие</th>
                        <td><?= $product['quantity'] > 0 ? 'В наличии (' . $product['quantity'] . ' шт.)' : 'Под заказ' ?></td>
                    </tr>
                    <?php if (!empty($product['brand_name'])): ?>
                    <tr>
                        <th>Бренд</th>
                        <td><?= htmlspecialchars($product['brand_name']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['category_name'])): ?>
                    <tr>
                        <th>Категория</th>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <form method="post" action="add_to_cart.php" class="add-to-cart">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" class="btn">Добавить в корзину</button>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>