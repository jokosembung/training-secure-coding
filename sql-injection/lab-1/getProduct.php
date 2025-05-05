<?php
require '../../connection.php';
$category = isset($_GET['category']) ? $_GET['category'] : null;
if ($category == null) {
    $sql = "SELECT p.id, p.name, p.price, p.thumbnail, pc.category_name
        FROM products p
        JOIN product_categories pc ON pc.id = p.product_category_id
        WHERE p.is_publish = true";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
} else {
    $category = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
    
    $sql = "SELECT p.id, p.name, p.price, p.thumbnail, pc.category_name
            FROM products p
            JOIN product_categories pc ON pc.id = p.product_category_id
            WHERE pc.category_name = ? AND p.is_publish = true";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category); // "s" means string
    $stmt->execute();
}
//var_dump($sql);
$result = $stmt->get_result();
