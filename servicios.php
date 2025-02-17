<?php
include 'includes/db.php';

// Obtener todos los servicios
try {
    $stmt = $pdo->query("SELECT * FROM servicios");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener tipos únicos para filtros
    $tipos_servicios = $pdo->query("SELECT DISTINCT tipo FROM servicios")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error al cargar servicios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Servicios Especializados - MOTOROOM</title>
</head>
<body>
    <header class="main-header">
        <?php include 'includes/navbar.php'; ?>
    </header>

    <main class="main-content">
        <section class="servicios-main">
            <div class="servicios-hero">
                <h1>Servicios para Alto Cilindraje</h1>
                <p>Tecnología de punta y expertise para tu máquina</p>
            </div>

            <!-- Filtros -->
            <div class="servicios-filtros">
                <div class="filtros-container">
                    <button class="filtro-btn active" data-tipo="todos">Todos</button>
                    <?php foreach ($tipos_servicios as $tipo): ?>
                    <button class="filtro-btn" data-tipo="<?= htmlspecialchars($tipo) ?>">
                        <?= htmlspecialchars(ucfirst($tipo)) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Grid de Servicios -->
            <div class="servicios-grid">
                <?php foreach ($servicios as $servicio): ?>
                <div class="servicio-card" data-tipo="<?= htmlspecialchars($servicio['tipo']) ?>">
                    <div class="servicio-header">
                        <div class="servicio-icono">
                            <i class="<?= htmlspecialchars($servicio['icono']) ?>"></i>
                        </div>
                        <h2><?= htmlspecialchars($servicio['nombre']) ?></h2>
                    </div>
                    
                    <div class="servicio-body">
                        <p class="servicio-descripcion"><?= htmlspecialchars($servicio['descripcion']) ?></p>
                        
                        <div class="servicio-especificaciones">
                            <div class="espec-item">
                                <i class="fas fa-clock"></i>
                                <span><?= $servicio['duracion'] ?> min</span>
                            </div>
                            <div class="espec-item">
                                <i class="fas fa-certificate"></i>
                                <span>Garantía <?= $servicio['garantia'] ?> meses</span>
                            </div>
                        </div>
                    </div>

                    <div class="servicio-footer">
                        <div class="servicio-precio">
                            Desde $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                        </div>
                        <a href="citas.php?servicio_id=<?= $servicio['id'] ?>" class="btn-agendar">
                            <i class="fas fa-calendar-alt"></i> Agendar
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
    // Filtrado de servicios
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tipo = this.dataset.tipo;
            
            // Actualizar botones activos
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar servicios
            document.querySelectorAll('.servicio-card').forEach(card => {
                if(tipo === 'todos' || card.dataset.tipo === tipo) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>