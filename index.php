<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>MOTOROOM - Tienda de Motocicletas</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main>
    
        <h1>Bienvenido a MOTOROOM</h1>
        <p>Tu tienda de venta y mantenimiento de motocicletas.</p>
        <img src="Texto del párrafo (1).gif" alt="Logo de MOTOROOM" class="logo-animado">
        
        <div class="contenedor">
    <section>
        <a href="productos.php">
            <div class="image-container">
                <img src="bmw-07.jpg" alt="Moto" class="imagen-producto">
                <h2>Productos Destacados</h2>
            </div>
        </a>
    </section>

    <section>
        <a href="servicios.php">
            <div class="image-container">
                <img src="Mecanica.jpg" alt="Mecanica" class="imagen-producto">
                <h2>Nuestros Servicios</h2>
            </div>
        </a>
    </section>
</div>
    </main>

   
    <?php include 'includes/footer.php'; ?>
</body>
</html>