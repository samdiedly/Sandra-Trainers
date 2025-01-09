<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['id'])) {
    $usuario_logueado = false;
} else {
    $usuario_logueado = true;
    $usuario_id = $_SESSION['id'];
    $usuario_tipo = $_SESSION['tipo'];  // "administrador" o "cliente"
    $nombre_usuario = $_SESSION['nombre']; // Nombre del usuario logueado
}

// Obtener los productos disponibles
$conexion = new mysqli("localhost", "root", "password123", "tienda_deportiva");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Zapatillas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        .product {
            display: inline-block;
            width: 30%;
            margin: 15px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
        }
        .product h3 {
            margin: 10px 0;
        }
        .product p {
            font-size: 16px;
            color: #777;
        }
        .product .price {
            font-size: 18px;
            color: green;
        }
        .navbar {
            background-color: #333;
            padding: 10px;
            color: white;
            text-align: center;
        }
        .navbar a {
            color: white;
            padding: 10px;
            text-decoration: none;
            margin: 0 10px;
        }
        .navbar a:hover {
            background-color: #575757;
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <div class="navbar">
        <a href="index.php">Catálogo</a>
        <?php if ($usuario_logueado): ?>
            <a href="carrito.php">Carrito</a>
            <span>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>!</span> <!-- Nombre del usuario logueado -->
            <a href="logout.php">Cerrar sesión</a>
            
            <?php if ($usuario_tipo == "administrador"): ?>
                <a href="usuarios.php">Usuarios</a> <!-- Botón para gestionar usuarios -->
                <a href="productos.php">Productos</a> <!-- Botón para gestionar productos -->
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
            <a href="registro.php">Registrarse</a>
        <?php endif; ?>
    </div>

    <h1>Bienvenido a la Tienda de Zapatillas: Sandra-Trainers </h1>

    <!-- Mostrar productos -->
    <div class="products">
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($producto = $resultado->fetch_assoc()): ?>
                <div class="product">
                    <img src="imagenes/zapatilla<?php echo $producto['id']; ?>.jpg" alt="Producto">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?></p>
                    <?php if ($usuario_logueado && $usuario_tipo != "administrador"): ?>
                        <form method="POST" action="procesar_carrito.php">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit">Añadir al carrito</button>
                        </form>
                    <?php else: ?>
                        <p><em>Debes iniciar sesión para añadir al carrito.</em></p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay productos disponibles.</p>
        <?php endif; ?>
    </div>

    <?php
    $conexion->close();
    ?>

</body>
</html>
