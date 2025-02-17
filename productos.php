<?php
include 'includes/db.php';

// Obtener parámetros de filtrado
$categoria = $_GET['categoria'] ?? null;
$marca = $_GET['marca'] ?? null;
$min_cc = $_GET['min_cc'] ?? null;
$max_precio = $_GET['max_precio'] ?? null;

// Construir consulta base
$sql = "SELECT * FROM productos WHERE stock > 0";
$params = [];
$conditions = [];

// Filtros
if ($categoria) {
    $conditions[] = "categoria = ?";
    $params[] = $categoria;
}

if ($marca) {
    $conditions[] = "marca = ?";
    $params[] = $marca;
}

if ($min_cc) {
    $conditions[] = "cilindrada >= ?";
    $params[] = $min_cc;
}

if ($max_precio) {
    $conditions[] = "precio <= ?";
    $params[] = $max_precio;
}

// Combinar condiciones
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener marcas y categorías únicas para los filtros
    $marcas = $pdo->query("SELECT DISTINCT marca FROM productos ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    die("Error al cargar productos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Catálogo de Motos - MOTOROOM</title>
</head>
<body>
    <header class="main-header">
        <?php include 'includes/navbar.php'; ?>
    </header>

    <main class="main-content">
        <section class="productos-container">
            <h1 class="section-title">Nuestro Catálogo</h1>
            
            <!-- Filtros -->
            <div class="filtros-sidebar">
                <form class="filtros-form">
                    <div class="filtro-group">
                        <h3>Categorías</h3>
                        <select name="categoria" class="select-filter">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat == $categoria ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($cat)) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-group">
                        <h3>Marcas</h3>
                        <select name="marca" class="select-filter">
                            <option value="">Todas</option>
                            <?php foreach ($marcas as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>" <?= $m == $marca ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-group">
                        <h3>Cilindrada Mínima</h3>
                        <input type="number" name="min_cc" 
                               value="<?= htmlspecialchars($min_cc) ?>" 
                               placeholder="Ej: 600" min="0">
                    </div>

                    <div class="filtro-group">
                        <h3>Precio Máximo</h3>
                        <input type="number" name="max_precio" 
                               value="<?= htmlspecialchars($max_precio) ?>" 
                               placeholder="USD" min="0">
                    </div>

                    <button type="submit" class="btn-filtrar">
                        <i class="fas fa-filter"></i> Aplicar Filtros
                    </button>
                </form>
            </div>

            <!-- Listado de Productos -->
            <div class="productos-grid">
                <?php if (empty($productos)): ?>
                <div class="no-result">
                    <i class="fas fa-motorcycle"></i>
                    <p>No encontramos motos con esos filtros</p>
                </div>
                <?php endif; ?>

                <?php foreach ($productos as $producto): ?>
                <article class="producto-card">
                    <div class="producto-imagen">
                        <img src="img/motos/<?= htmlspecialchars($producto['imagen_principal']) ?>" 
                             alt="<?= htmlspecialchars($producto['marca'] . ' ' . $producto['modelo']) ?>">
                        <span class="categoria-badge">
                            <?= htmlspecialchars($producto['categoria']) ?>
                        </span>
                    </div>
                    
                    <div class="producto-info">
                        <h2><?= htmlspecialchars($producto['marca']) ?></h2>
                        <h3><?= htmlspecialchars($producto['modelo']) ?></h3>
                        
                        <div class="especificaciones">
                            <div class="espec-item">
                                <i class="fas fa-tachometer-alt"></i>
                                <span><?= $producto['cilindrada'] ?>cc</span>
                            </div>
                            <div class="espec-item">
                                <i class="fas fa-weight-hanging"></i>
                                <span><?= $producto['peso'] ?>kg</span>
                            </div>
                            <div class="espec-item">
                                <i class="fas fa-horse-head"></i>
                                <span><?= $producto['potencia'] ?>HP</span>
                            </div>
                        </div>

                        <div class="producto-precio">
                            $<?= number_format($producto['precio'], 0, ',', '.') ?>
                        </div>

                        <div class="producto-acciones">
                            <a href="detalles.php?id=<?= $producto['id'] ?>" 
                               class="btn-detalles">
                               <i class="fas fa-search"></i> Detalles
                            </a>
                            <button class="btn-cotizar" 
                                    data-producto-id="<?= $producto['id'] ?>">
                                <i class="fas fa-file-invoice-dollar"></i> Cotizar
                            </button>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
    // Filtrado dinámico sin recargar la página
    document.querySelectorAll('.select-filter, .filtros-form input').forEach(element => {
        element.addEventListener('change', () => {
            document.querySelector('.filtros-form').submit();
        });
    });
    </script>
</body>
</html>