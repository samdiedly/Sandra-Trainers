<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'admin') {
    header('Location: login.php');
    exit();
}

$conexion = new mysqli('localhost', 'root', 'password123', 'tienda_deportiva');
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener todos los usuarios
$sql_usuarios = "SELECT * FROM usuarios";
$resultado_usuarios = $conexion->query($sql_usuarios);

// Obtener todos los productos
$sql_productos = "SELECT * FROM productos";
$resultado_productos = $conexion->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Panel de Administración</h1>
        <nav>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <h2>Usuarios</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
            <?php
            while($usuario = $resultado_usuarios->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $usuario['id'] . "</td>";
                echo "<td>" . $usuario['nombre'] . "</td>";
                echo "<td>" . $usuario['correo'] . "</td>";
                echo "<td>" . $usuario['tipo'] . "</td>";
                echo "<td><a href='editar_usuario.php?id=" . $usuario['id'] . "'>Editar</a> | <a href='eliminar_usuario.php?id=" . $usuario['id'] . "'>Eliminar</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h2>Productos</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php
            while($producto = $resultado_productos->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $producto['id'] . "</td>";
                echo "<td>" . $producto['nombre'] . "</td>";
                echo "<td>" . number_format($producto['precio'], 2) . "</td>";
                echo "<td><a href='editar_producto.php?id=" . $producto['id'] . "'>Editar</a> | <a href='eliminar_producto.php?id=" . $producto['id'] . "'>Eliminar</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </main>
</body>
</html>

<?php
$conexion->close();
?>

