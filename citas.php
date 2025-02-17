<?php
include 'includes/db.php';

// Procesar formulario de citas
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $servicio_id = $_POST['servicio_id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $moto = $_POST['moto'];
        $observaciones = $_POST['observaciones'];

        // Validaciones básicas
        if (empty($servicio_id) || empty($fecha) || empty($hora) || empty($nombre)) {
            throw new Exception("Todos los campos requeridos deben ser llenados");
        }

        $stmt = $pdo->prepare("INSERT INTO citas_taller 
            (servicio_id, fecha, nombre, telefono, email, moto, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $fecha_completa = date('Y-m-d H:i:s', strtotime("$fecha $hora"));
        
        $stmt->execute([
            $servicio_id,
            $fecha_completa,
            $nombre,
            $telefono,
            $email,
            $moto,
            $observaciones
        ]);

        $success = "¡Cita agendada exitosamente! Nos comunicaremos para confirmar.";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener servicios disponibles
try {
    $stmt = $pdo->query("SELECT * FROM servicios");
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar servicios: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'includes/header.php'; ?>
    <title>Agendar Cita - MOTOROOM</title>
</head>
<body>
    <header class="main-header">
        <?php include 'includes/navbar.php'; ?>
    </header>

    <main class="main-content">
        <section class="cita-section">
            <h1 class="section-title">Agendar Cita en el Taller</h1>
            
            <?php if ($error): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert success">
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <form class="cita-form" method="POST">
                <div class="form-grid">
                    <!-- Columna Izquierda -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="servicio_id">Servicio Requerido:</label>
                            <select name="servicio_id" id="servicio_id" required>
                                <option value="">Seleccione un servicio</option>
                                <?php foreach ($servicios as $servicio): ?>
                                <option value="<?= $servicio['id'] ?>">
                                    <?= htmlspecialchars($servicio['nombre']) ?> - 
                                    $<?= number_format($servicio['precio'], 0, ',', '.') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha">Fecha:</label>
                            <input type="date" id="fecha" name="fecha" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="hora">Hora:</label>
                            <input type="time" id="hora" name="hora" 
                                   min="08:00" max="18:00" required>
                            <small>Horario de atención: 8:00 AM - 6:00 PM</small>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo:</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono:</label>
                            <input type="tel" id="telefono" name="telefono" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico:</label>
                            <input type="email" id="email" name="email">
                        </div>

                        <div class="form-group">
                            <label for="moto">Moto (Marca/Modelo):</label>
                            <input type="text" id="moto" name="moto" required>
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones" name="observaciones" rows="4"></textarea>
                </div>

                <button type="submit" class="cta-button">
                    <i class="fas fa-calendar-check"></i> Confirmar Cita
                </button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
    // Validación de fecha y hora en cliente
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        
        // Deshabilitar fines de semana
        fechaInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const day = selectedDate.getDay();
            
            if (day === 0 || day === 6) {
                alert('Solo trabajamos de lunes a viernes');
                this.value = '';
            }
        });

        // Validar hora fuera del horario laboral
        horaInput.addEventListener('change', function() {
            const hora = parseInt(this.value.split(':')[0]);
            if (hora < 8 || hora >= 18) {
                alert('Horario no válido');
                this.value = '';
            }
        });
    });
    </script>
</body>
</html>