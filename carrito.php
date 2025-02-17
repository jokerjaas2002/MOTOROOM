<?php
session_start();
include 'includes/db.php';

// Acciones del carrito
if (isset($_GET['action'])) {
    $producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    switch ($_GET['action']) {
        case 'add':
            if (isset($_POST['cantidad'])) {
                agregarAlCarrito($pdo, $producto_id, intval($_POST['cantidad']));
            }
            break;
            
        case 'remove':
            eliminarDelCarrito($producto_id);
            break;
            
        case 'update':
            if (isset($_POST['cantidad'])) {
                actualizarCantidad($producto_id, intval($_POST['cantidad']));
            }
            break;
            
        case 'clear':
            unset($_SESSION['carrito']);
            break;
    }
    
    header('Location: carrito.php');
    exit;
}

// Funciones del carrito
function agregarAlCarrito($pdo, $producto_id, $cantidad) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? AND stock > 0");
        $stmt->execute([$producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto && $cantidad > 0) {
            $item = [
                'id' => $producto['id'],
                'nombre' => $producto['marca'] . ' ' . $producto['modelo'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto['imagen_principal'],
                'stock' => $producto['stock']
            ];
            
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$producto_id] = $item;
            }
            
            // No exceder stock disponible
            if ($_SESSION['carrito'][$producto_id]['cantidad'] > $producto['stock']) {
                $_SESSION['carrito'][$producto_id]['cantidad'] = $producto['stock'];
            }
        }
    } catch (PDOException $e) {
        error_log("Error al agregar producto: " . $e->getMessage());
    }
}

function eliminarDelCarrito($producto_id) {
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
    }
}

function actualizarCantidad($producto_id, $cantidad) {
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = max(1, min($cantidad, $_SESSION['carrito'][$producto_id]['stock']));
    }
}

// Calcular totales
$total_items = 0;
$total_precio = 0;

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
        $total_precio += $item['precio'] * $item['cantidad'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Carrito de Compras - MOTOROOM</title>
</head>
<body>
    <header class="main-header">
        <?php include 'includes/navbar.php'; ?>
    </header>

    <main class="main-content">
        <section class="carrito-container">
            <h1 class="section-title">Tu Carrito de Compras</h1>
            
            <?php if (empty($_SESSION['carrito'])): ?>
            <div class="carrito-vacio">
                <i class="fas fa-shopping-cart"></i>
                <p>Tu carrito está vacío</p>
                <a href="productos.php" class="cta-button">Ver Motos Disponibles</a>
            </div>
            <?php else: ?>
            <div class="carrito-grid">
                <!-- Lista de Productos -->
                <div class="carrito-items">
                    <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div class="carrito-item">
                        <img src="img/motos/<?= htmlspecialchars($item['imagen']) ?>" 
                             alt="<?= htmlspecialchars($item['nombre']) ?>">
                             
                        <div class="item-info">
                            <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                            <p class="item-price">$<?= number_format($item['precio'], 0, ',', '.') ?></p>
                            
                            <div class="item-actions">
                                <form method="post" action="carrito.php?action=update&id=<?= $item['id'] ?>">
                                    <div class="cantidad-selector">
                                        <button type="button" class="cantidad-btn menos">-</button>
                                        <input type="number" name="cantidad" 
                                               value="<?= $item['cantidad'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>">
                                        <button type="button" class="cantidad-btn mas">+</button>
                                    </div>
                                    <button type="submit" class="btn-actualizar">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                                
                                <a href="carrito.php?action=remove&id=<?= $item['id'] ?>" 
                                   class="btn-eliminar">
                                   <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Resumen de Compra -->
                <div class="resumen-compra">
                    <div class="resumen-container">
                        <h2>Resumen de Compra</h2>
                        
                        <div class="resumen-detalle">
                            <p>Total Items: <span><?= $total_items ?></span></p>
                            <p>Subtotal: <span>$<?= number_format($total_precio, 0, ',', '.') ?></span></p>
                            <p>Envío: <span class="calcular-envio">Calcular</span></p>
                            <p class="total">Total: <span>$<?= number_format($total_precio, 0, ',', '.') ?></span></p>
                        </div>
                        
                        <div class="resumen-acciones">
                            <a href="productos.php" class="cta-outline">
                                <i class="fas fa-arrow-left"></i> Seguir Comprando
                            </a>
                            <a href="checkout.php" class="cta-button">
                                <i class="fas fa-lock"></i> Finalizar Compra
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
    // Control de cantidad
    document.querySelectorAll('.cantidad-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            let value = parseInt(input.value);
            
            if (this.classList.contains('menos')) {
                value = Math.max(1, value - 1);
            } else {
                value = Math.min(<?= $item['stock'] ?? 0 ?>, value + 1);
            }
            
            input.value = value;
        });
    });

    // Mostrar notificación al agregar items
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('added')) {
    showNotification('🛒 Producto agregado al carrito');
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'carrito-notification';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}
    </script>
</body>
</html>