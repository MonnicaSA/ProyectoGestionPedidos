<?php
include 'config.php';
session_start();

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors');

// Verificar que el usuario está autenticado
if (!isset($_SESSION['num_empleado']) || !isset($_SESSION['rol_empleado'])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

$num_empleado = $_SESSION['num_empleado'];

// **MÉTODO GET - Listar pedidos**
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT p.id_pedido, p.fecha, p.estado, 
                   GROUP_CONCAT(pr.nombre, ':', dp.cantidad_producto SEPARATOR ',') AS productos
            FROM pedidos p
            LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
            LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
            WHERE p.num_empleado = ?
            GROUP BY p.id_pedido
            ORDER BY p.estado, p.fecha";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$num_empleado]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear productos en un array con nombre en vez de ID
    foreach ($pedidos as &$pedido) {
        $productosArray = [];
        if (!empty($pedido["productos"])) {
            $productosList = explode(",", $pedido["productos"]);
            foreach ($productosList as $producto) {
                list($nombre, $cantidad) = explode(":", $producto);
                $productosArray[] = ["nombre" => $nombre, "cantidad" => $cantidad];
            }
        }
        $pedido["productos"] = $productosArray;
    }

    echo json_encode($pedidos ?: []);
    exit;
}


// **MÉTODO POST - Crear pedido y detalle_pedido**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $estado = $data['estado'] ?? 'Pendiente';
    $productos = $data['productos'] ?? [];

    try {
        $pdo->beginTransaction();
        
        // Insertar el pedido en la tabla `pedidos`
        $sql = "INSERT INTO pedidos (num_empleado, estado) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$num_empleado, $estado]);
        $id_pedido = $pdo->lastInsertId();

        // Insertar los detalles del pedido en la tabla `detalle_pedido`
        foreach ($productos as $producto) {
            $id_producto = $producto['id_producto'];
            $cantidad = $producto['cantidad'];

            $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad_producto) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_pedido, $id_producto, $cantidad]);
        }

        $pdo->commit();
        echo json_encode(["message" => "Pedido y detalles creados con éxito"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["error" => "Error al crear el pedido: " . $e->getMessage()]);
    }
    exit;
}
