<?php
session_start();

// Debugging errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Verifica si el usuario tiene privilegios de administrador
if ($_SESSION['tipo'] != "administrador") {
    header("Location: index.php");
    exit();
}
// Establece conexión con la base de datos
$conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener usuarios existentes
$sql = "SELECT * FROM usuarios";
$resultado = $conexion->query($sql);
// Procesa las acciones del formulario si se envía una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
        // Agregar nuevo usuario
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
        $tipo = $_POST['tipo'];

        $sql = "INSERT INTO usuarios (nombre, correo, contraseña, tipo) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $contraseña, $tipo);
        $stmt->execute();
        header("Location: usuarios.php");
        exit();
    } elseif (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        // Eliminar usuario
        $usuario_id = $_POST['usuario_id'];
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        header("Location: usuarios.php");
        exit();
    } elseif (isset($_POST['accion']) && $_POST['accion'] == 'modificar') {
        // Modificar usuario
        $usuario_id = $_POST['usuario_id'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $tipo = $_POST['tipo'];
        $contraseña = $_POST['contraseña'];

        if (!empty($contraseña)) {
            // Si se proporciona una nueva contraseña, actualízala
            $contraseña_hash = password_hash($contraseña, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nombre = ?, correo = ?, tipo = ?, contraseña = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssi", $nombre, $correo, $tipo, $contraseña_hash, $usuario_id);
        } else {
            // Si no se proporciona contraseña, no la actualices
            $sql = "UPDATE usuarios SET nombre = ?, correo = ?, tipo = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $nombre, $correo, $tipo, $usuario_id);
        }

        $stmt->execute();
        header("Location: usuarios.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
</head>
<body>

<h1>Gestión de Usuarios</h1>

<!-- Formulario para agregar un nuevo usuario -->
<h2>Agregar usuario</h2>
<form method="POST" action="usuarios.php">
    <input type="hidden" name="accion" value="agregar">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br><br>

    <label for="correo">Correo electrónico:</label>
    <input type="email" id="correo" name="correo" required><br><br>

    <label for="contraseña">Contraseña:</label>
    <input type="password" id="contraseña" name="contraseña" required><br><br>

    <label for="tipo">Tipo de usuario:</label>
    <select name="tipo" id="tipo">
        <option value="cliente">Cliente</option>
        <option value="administrador">Administrador</option>
    </select><br><br>

    <button type="submit">Agregar usuario</button>
</form>

<h2>Usuarios existentes</h2>
<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Tipo</th>
        <th>Acciones</th>
    </tr>
    <?php while ($usuario = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
            <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
            <td>
                <!-- Botón para eliminar el usuario -->
                <form method="POST" action="usuarios.php" style="display:inline;">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <button type="submit">Eliminar</button>
                </form>
                
                <!-- Botón para modificar el usuario -->
                <button onclick="document.getElementById('modificar-<?php echo $usuario['id']; ?>').style.display='block'">Modificar</button>

                <!-- Formulario de edición de usuario -->
                <div id="modificar-<?php echo $usuario['id']; ?>" style="display:none;">
                    <h3>Modificar usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                    <form method="POST" action="usuarios.php">
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">

                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required><br><br>

                        <label for="correo">Correo electrónico:</label>
                        <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required><br><br>

                        <label for="tipo">Tipo de usuario:</label>
                        <select name="tipo" required>
                            <option value="cliente" <?php if ($usuario['tipo'] == 'cliente') echo 'selected'; ?>>Cliente</option>
                            <option value="administrador" <?php if ($usuario['tipo'] == 'administrador') echo 'selected'; ?>>Administrador</option>
                        </select><br><br>

                        <label for="contraseña">Nueva contraseña (opcional):</label>
                        <input type="password" name="contraseña"><br><br>

                        <button type="submit">Actualizar usuario</button>
                        <button type="button" onclick="document.getElementById('modificar-<?php echo $usuario['id']; ?>').style.display='none'">Cancelar</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

