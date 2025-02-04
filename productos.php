<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Productos - MOTOROOM</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <h1>Productos Destacados</h1>
        
        <!-- Formulario de búsqueda -->
        <form method="GET" action="productos.php">
            <input type="text" name="search" placeholder="Buscar productos..." required>
            <button type="submit">Buscar</button>
        </form>

        <?php
        include 'includes/db.php'; // Asegúrate de incluir la conexión a la base de datos

        // Obtener el término de búsqueda
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Consulta para obtener los productos
        $sql = "SELECT * FROM productos WHERE nombre LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['search' => '%' . $search . '%']);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica si hay productos
        if (count($productos) > 0) {
            echo '<section>';
            echo '<h2>Resultados de la Búsqueda</h2>';
            echo '<ul>';
            foreach ($productos as $producto) {
                echo '<li>';
                echo '<h3><a href="detalle_producto.php?id=' . $producto['id'] . '">' . htmlspecialchars($producto['nombre']) . '</a></h3>';
                echo '<p>' . htmlspecialchars($producto['descripcion']) . '</p>';
                echo '<p>Precio: $' . htmlspecialchars($producto['precio']) . '</p>';
                echo '<img src="' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre']) . '" style="width:150px;">';
                echo '</li>';
            }
            echo '</ul>';
            echo '</section>';
        } else {
            echo '<p>No se encontraron productos que coincidan con tu búsqueda.</p>';
        }
        ?>
        
        <section>
            <h2>Accesorios</h2>
            <ul>
                <li>
                    <h3>Casco Integral</h3>
                    <p>Casco de alta seguridad y comodidad, ideal para cualquier tipo de motocicleta.</p>
                </li>
                <li>
                    <h3>Guantes de Cuero</h3>
                    <p>Guantes resistentes y cómodos para una mejor sujeción y protección.</p>
                </li>
                <li>
                    <h3>Chaqueta de Moto</h3>
                    <p>Chaqueta con protección y ventilación, perfecta para cualquier clima.</p>
                </li>
            </ul>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>