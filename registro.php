<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     // Obtiene y limpia los datos ingresados por el usuario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
    // Establece conexión con la base de datos
    $conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }
    // Inserta un nuevo usuario como cliente en la base de datos
    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, tipo) VALUES (?, ?, ?, 'cliente')";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $contraseña);
    // Ejecuta la consulta y verifica si fue exitosa
    if ($stmt->execute()) {
        echo "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    // Cierra las conexiones
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

