<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, redirigir a login
    header('Location: login.php');
    exit();
}

$conexion = new mysqli('localhost', 'root', 'password123', 'tienda_deportiva');
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener los productos de la base de datos
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Zapatillas - Tienda Deportiva</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Catálogo de Zapatillas</h1>
        <nav>
            <a href="index.html">Volver al inicio</a> | 
            <a href="carrito.php">Carrito</a> | 
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <section id="productos">
            <?php
            if ($resultado->num_rows > 0) {
                while($producto = $resultado->fetch_assoc()) {
                    echo '<div class="producto">';
                    echo '<img src="img/' . $producto['imagen'] . '" alt="' . $producto['nombre'] . '">';
                    echo '<h3>' . $producto['nombre'] . '</h3>';
                    echo '<p>' . $producto['descripcion'] . '</p>';
                    echo '<p>Precio: $' . number_format($producto['precio'], 2) . '</p>';
                    echo '<a href="carrito.php?producto_id=' . $producto['id'] . '">Añadir al carrito</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay productos disponibles en el catálogo.</p>';
            }
            ?>
        </section>
    </main>
</body>
</html>

<?php
$conexion->close();
?>

