<?php
// Incluir solo la conexión a la base de datos
include 'includes/db.php';

// Obtener productos directamente
try {
    $stmt_productos = $pdo->query("SELECT * FROM productos WHERE stock > 0 LIMIT 8");
    $productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $productos = [];
    error_log("Error al obtener productos: " . $e->getMessage());
}

// Obtener servicios directamente
try {
    $stmt_servicios = $pdo->query("SELECT * FROM servicios");
    $servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $servicios = [];
    error_log("Error al obtener servicios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/header.php'; ?>
</head>
<body>
    <header class="main-header">
        <!-- El mismo header que antes -->
    </header>

    <main class="main-content">
        <!-- Sección Hero -->
        <section class="hero-banner">
            <div class="hero-content">
                <h1>MOTOROOM - Pasión por el Alto Cilindraje</h1>
                <p>Venta y servicio técnico especializado</p>
            </div>
        </section>

        <!-- Catálogo de Motos -->
        <section class="motos-destacadas">
            <h2>Nuestras Motos Destacadas</h2>
            <div class="moto-grid">
                <?php foreach ($productos as $moto): ?>
                <div class="moto-card">
                    <img src="<?= htmlspecialchars($moto['imagen_principal']) ?>" 
                         alt="<?= htmlspecialchars($moto['marca'] . ' ' . $moto['modelo']) ?>">
                    <div class="moto-info">
                        <h3><?= htmlspecialchars($moto['marca']) ?> <?= htmlspecialchars($moto['modelo']) ?></h3>
                        <div class="especificaciones">
                            <p><i class="fas fa-tachometer-alt"></i> <?= $moto['cilindrada'] ?>cc</p>
                            <p><i class="fas fa-money-bill-wave"></i> $<?= number_format($moto['precio'], 0, ',', '.') ?></p>
                        </div>
                        <a href="detalle.php?id=<?= $moto['id'] ?>" class="btn-ver-detalle">
                            <i class="fas fa-search"></i> Ver Detalles
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Servicios del Taller -->
        <section class="servicios-taller">
            <h2>Nuestros Servicios</h2>
            <div class="servicios-container">
                <?php foreach ($servicios as $servicio): ?>
                <div class="servicio-card">
                    <div class="servicio-icono">
                        <i class="<?= htmlspecialchars($servicio['icono']) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                    <p class="servicio-descripcion"><?= htmlspecialchars($servicio['descripcion']) ?></p>
                    <div class="servicio-precio">
                        Desde $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                    </div>
                    <button class="btn-agendar" data-servicio-id="<?= $servicio['id'] ?>">
                        <i class="fas fa-calendar-check"></i> Agendar
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>