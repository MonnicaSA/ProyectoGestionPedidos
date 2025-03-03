<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, //Cambiar a true si usas https
    'httponly' => true,
]);

session_start();
include "config.php";

header('Content-Type: application/json');

// Configuración de errores
error_reporting(E_ALL);


ini_set('display_errors', 1); // Muestra los errores en el navegador
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');


// Verificar si el usuario está autenticado
if (!isset($_SESSION["num_empleado"]) || !isset($_SESSION["rol_empleado"])) {

    echo json_encode(["message" => "Acceso denegado"]);
    exit;
}

$num_empleado = $_SESSION["num_empleado"]; // Número de empleado desde la sesión
$rol_empleado = $_SESSION["rol_empleado"]; // Rol del empleado desde la sesión

// Insertar un pedido
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        try {
            // Leer datos enviados en el cuerpo de la petición
            $data = json_decode(file_get_contents("php://input"), true); // Usamos true para array asociativo

            // Verificar que haya productos y que sea un array
            if (!isset($data['productos']) || !is_array($data['productos'])) {

                echo json_encode(["message" => "Datos incorrectos: Se esperaba un array de productos"]);
                exit;
            }

            $pdo->beginTransaction(); // Inicia la transacción

            $precio_total = 0;

            foreach ($data['productos'] as $producto) {
                $queryPrecio = "SELECT precio_unitario FROM productos WHERE id_producto = ?";
                $stmPrecio = $pdo->prepare($queryPrecio);
                $stmPrecio->execute([$producto['id_producto']]);
                $precio = $stmPrecio->fetchColumn();

                if ($precio === false) {
                    throw new Exception("El producto con ID {$producto['id_producto']} no existe");
                }
                $precio_total += $precio * $producto['cantidad'];
            }

            // Insertar los productos en el pedido
            $query = "INSERT INTO pedidos (num_empleado, precio_total) VALUES (?, ?)";
            $stm = $pdo->prepare($query);
            $stm->execute([$num_empleado, $precio_total]);
            $id_pedido = $pdo->lastInsertId(); // Se guarda el último pedido

            // Insertar los productos en detalle_pedido
            $queryDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad_producto) VALUES (?, ?, ?)";
            $stmDetalle = $pdo->prepare($queryDetalle);

            // En cada iteración se inserta un producto del pedido en detalle_pedido usando el mismo id_pedido para todos
            foreach ($data['productos'] as $producto) {
                $stmDetalle->execute([$id_pedido, $producto['id_producto'], $producto['cantidad']]);
            }

            $pdo->commit(); // Confirma la transacción
            echo json_encode(["message" => "Pedido registrado correctamente", "id_pedido" => $id_pedido]);
        } catch (PDOException $e) {
            $pdo->rollBack(); // Revierte si hay error
            echo json_encode(["error" => "Error al registrar el pedido: " . $e->getMessage()]);
            exit;
        }
        break;

    case "GET":
        try {
            // Verificar si se está solicitando el detalle de un pedido específico
            if (isset($_GET['id'])) {
                $idPedido = $_GET['id'];

                $query = "SELECT pr.nombre AS producto, dp.cantidad_producto, pr.precio_unitario 
                FROM detalle_pedido dp
                JOIN productos pr ON dp.id_producto = pr.id_producto
                WHERE dp.id_pedido = ?";
                $stm = $pdo->prepare($query);
                $stm->execute([$idPedido]);
                $detallePedido = $stm->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(["pedidos" => $detallePedido]);
            } else {
                // Consulta para listar todos los pedidos (sin detalles individuales)
                if ($rol_empleado === "Administrador") {
                    $query = " SELECT p.id_pedido, p.precio_total, p.fecha, p.num_empleado, p.estado, e.nombre AS empleado, GROUP_CONCAT(CONCAT(pr.nombre, ' (', dp.cantidad_producto, ')') SEPARATOR ', ') AS productos
                    FROM pedidos p JOIN empleados e ON p.num_empleado = e.num_empleado
                    JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                    JOIN productos pr ON dp.id_producto = pr.id_producto
                    GROUP BY p.id_pedido
                    ORDER BY p.fecha DESC";
                    $stm = $pdo->prepare($query);
                    $stm->execute();
                } else {
                    $query = "SELECT p.id_pedido, p.precio_total, p.fecha, p.num_empleado, p.estado, e.nombre AS empleado,
                        GROUP_CONCAT(CONCAT(pr.nombre, ' (', dp.cantidad_producto, ')') SEPARATOR ', ') AS productos
                    FROM pedidos p
                    JOIN empleados e ON p.num_empleado = e.num_empleado
                    JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                    JOIN productos pr ON dp.id_producto = pr.id_producto
                    WHERE p.num_empleado = ?
                    GROUP BY p.id_pedido
                    ORDER BY p.fecha DESC";
                    $stm = $pdo->prepare($query);
                    $stm->execute([$num_empleado]);
                }

                $pedidos = $stm->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["pedidos" => $pedidos]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al obtener pedidos: " . $e->getMessage()]);
        }
        break;


    case "PUT":
        try {
            $data = json_decode(file_get_contents("php://input"));

            // // Validar permisos (solo Administrador puede actualizar)
            // if ($rol_empleado !== "Administrador") {
            //     echo json_encode(["message" => "Solo los administradores pueden actualizar pedidos"]);
            //     exit;
            // }

            // Validar datos recibidos
            if (!isset($data->id_pedido) || !isset($data->estado)) {
                echo json_encode(["message" => "Faltan campos obligatorios"]);
                exit;
            }


            // Validar que el estado sea válido
            $estadosPermitidos = ['Pendiente', 'En preparación', 'Entregado'];
            if (!in_array($data->estado, $estadosPermitidos)) {
                echo json_encode(["message" => "Estado no válido. Los estados permitidos son: 'Pendiente', 'En preparación', 'Entregado'."]);
                exit;
            }

            $id_pedido = $data->id_pedido;
            $estado = $data->estado;

            // Actualizar el pedido
            $query = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
            $stm = $pdo->prepare($query);
            $stm->execute([$estado, $id_pedido]); // Orden correcto

            echo json_encode(["message" => "Pedido actualizado con éxito"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al actualizar el pedido: " . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        try {
            $data = json_decode(file_get_contents("php://input"));

            // Validar los permisos
            if ($rol_empleado !== "Administrador") {
                echo json_encode(["message" => "Solo puede eliminar pedidos el Administrador"]);
                exit;
            }

            // Validar los datos recibidos
            if (!isset($data->id_pedido)) {
                echo json_encode(["message" => "El campo id pedido está vacío"]);
                exit;
            }

            $id_pedido = $data->id_pedido;
            $query = "DELETE FROM pedidos WHERE id_pedido = ?";
            $stm = $pdo->prepare($query);
            $stm->execute([$id_pedido]);

            echo json_encode(["message" => "El pedido se ha eliminado con éxito"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "No se puede eliminar el pedido: " . $e->getMessage()]);
        }
        break;
}
