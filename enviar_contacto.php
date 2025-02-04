<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];

    // Aquí puedes agregar la lógica para enviar el correo o guardar en la base de datos

    echo "Gracias, $nombre. Tu mensaje ha sido enviado.";
} else {
    echo "Método no permitido.";
}
?>