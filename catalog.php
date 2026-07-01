<?php 
include 'includes/header.php';
include_once 'includes/db_connect.php';
?>

<main>
    <h1>Каталог</h1>
    
    <div class="filters">
        <form method="GET">
            <input type="text" name="search" placeholder="Найти всё что нужно...">
            <button type="submit">Найти</button>
        </form>
    </div>
    
    <div class="products">
        <?php
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $brand = $_GET['brand'] ?? '';
        
        $sql = "SELECT p.*, c.name AS category_name, b.name AS brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.name LIKE ?";
        
        $params = ["%$search%"];
        
        if (!empty($category)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category;
        }
        
        if (!empty($brand)) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $brand;
        }
        
        $stmt = mysqli_prepare($link, $sql);
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while($product = mysqli_fetch_assoc($result)):
        ?>
            <div class="product-card">
                <?php if (!empty($product['image'])): ?>
                    <img src="assets/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                <?php else: ?>
                    <div class="no-image">Нет изображения</div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><strong>Бренд:</strong> <?= htmlspecialchars($product['brand_name']) ?></p>
                <p><strong>Категория:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                <p><strong>Цена:</strong> <?= $product['price'] ?> BYN.</p>
                <p><strong>Наличие:</strong> <?= $product['quantity'] > 0 ? 'В наличии' : 'Под заказ' ?></p>
                <a href="product.php?id=<?= $product['id'] ?>" class="btn">Подробнее</a>
                <form method="post" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn">В корзину</button>
            </form>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>