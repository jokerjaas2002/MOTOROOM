<?php
include 'includes/db.php'; // Conexión a la base de datos

// Obtener el ID del producto de la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Consulta para obtener el producto específico
$sql = "SELECT * FROM productos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el producto existe
if (!$producto) {
    echo "<h1>Producto no encontrado</h1>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - MOTOROOM</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="width:300px;">
        <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
        <p>Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
        <a href="productos.php">Volver a Productos</a>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>