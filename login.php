<?php
// Inicia una nueva sesión o reanuda la existente
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpia y obtiene los datos ingresados por el usuario
    $correo = trim($_POST['correo']);
    $contraseña = $_POST['contraseña'];
    // Establece conexión con la base de datos
    $conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    // Consulta para buscar un usuario con el correo proporcionado
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {  // Verifica si se encontró un único usuario
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contraseña, $usuario['contraseña'])) { 
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['tipo'] = $usuario['tipo'];
            // Redirige al usuario a la página principal
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required><br><br>

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required><br><br>

        <button type="submit">Iniciar Sesión</button>
    </form>
</body>
</html>

