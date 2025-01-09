<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    $conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, tipo) VALUES (?, ?, ?, 'cliente')";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $contraseña);

    if ($stmt->execute()) {
        echo "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>
    <h1>Registro</h1>
    <form method="POST" action="registro.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required><br><br>

        <input type="submit" value="Registrarse">
    </form>
</body>
</html>

