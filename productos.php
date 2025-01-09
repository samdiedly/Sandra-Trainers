<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SESSION['tipo'] != "administrador") {
    header("Location: index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener productos existentes
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
        // Agregar nuevo producto
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
        $stmt->execute();
        header("Location: productos.php");
        exit();
    } elseif (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        // Eliminar producto
        $producto_id = $_POST['producto_id'];
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        header("Location: productos.php");
        exit();
    } elseif (isset($_POST['accion']) && $_POST['accion'] == 'modificar') {
        // Modificar producto
        $producto_id = $_POST['producto_id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $producto_id);
        $stmt->execute();
        header("Location: productos.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
</head>
<body>

<h1>Gestión de Productos</h1>

<!-- Formulario para agregar un nuevo producto -->
<h2>Agregar producto</h2>
<form method="POST" action="productos.php">
    <input type="hidden" name="accion" value="agregar">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" required><br><br>

    <label for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" required></textarea><br><br>

    <label for="precio">Precio:</label>
    <input type="number" step="0.01" id="precio" name="precio" required><br><br>

    <label for="stock">Stock:</label>
    <input type="number" id="stock" name="stock" required><br><br>

    <button type="submit">Agregar producto</button>
</form>

<h2>Productos existentes</h2>
<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th>
    </tr>
    <?php while ($producto = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
            <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
            <td>$<?php echo number_format($producto['precio'], 2); ?></td>
            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
            <td>
                <!-- Botón para eliminar el producto -->
                <form method="POST" action="productos.php" style="display:inline;">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <button type="submit">Eliminar</button>
                </form>
                
                <!-- Botón para modificar el producto -->
                <button onclick="document.getElementById('modificar-<?php echo $producto['id']; ?>').style.display='block'">Modificar</button>

                <!-- Formulario de edición de producto -->
                <div id="modificar-<?php echo $producto['id']; ?>" style="display:none;">
                    <h3>Modificar producto: <?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <form method="POST" action="productos.php">
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">

                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required><br><br>

                        <label for="descripcion">Descripción:</label>
                        <textarea name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea><br><br>

                        <label for="precio">Precio:</label>
                        <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required><br><br>

                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required><br><br>

                        <button type="submit">Actualizar producto</button>
                        <button type="button" onclick="document.getElementById('modificar-<?php echo $producto['id']; ?>').style.display='none'">Cancelar</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

