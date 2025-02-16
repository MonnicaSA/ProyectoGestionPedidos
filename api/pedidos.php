
<?php
session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, //Cambiar a true si usas https
    'httponly' => true,
]);


session_start();
include "config.php";


// Configuración de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');


// Verificar si el usuario está autenticado
if (!isset($_SESSION["num_empleado"]) || !isset($_SESSION["rol_empleado"])) {
    var_dump($_SESSION);
    echo json_encode(["message" => "Acceso denegado"]);
    exit;
}

$num_empleado = $_SESSION["num_empleado"]; // Número de empleado desde la sesión
$rol_empleado = $_SESSION["rol_empleado"]; // Rol del empleado desde la sesión

// Insertar un pedido
switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":
        try {
            //Leer datos enviados en el cuerpo de la petición
            $data = json_decode(file_get_contents("php://input"));
            // Verificar que haya productos y que sea un array
            if (!isset($data->productos) || !is_array($data->productos)) {
                var_dump($data->productos);
                echo json_encode(["message" => "Datos incorrectos: Se esperaba un array de productos"]);
                exit;
            }
                     
             $pdo->beginTransaction(); //  Inicia la transacción

             $precio_total = 0;
             
             foreach($data->productos as $producto){
              $queryPrecio = "SELECT precio FROM productos WHERE id_producto = ?";
              $stmPrecio = $pdo->prepare($queryPrecio);
              $stmPrecio->execute([$producto->id_producto]);
              $precio = $stmPrecio->fetchColumn();

              if($precio == false){
                throw new Exception("El producto con ID {$producto->id_producto} no existe");
              }
                $precio_total += $precio * $producto->cantidad;
             }
           // Insertar los productos en el pedido
            $query = "INSERT INTO pedidos (num_empleado, precio_total) VALUES (?, ?)";
            $stm = $pdo->prepare($query);
            $stm->execute([$num_empleado, $precio_total]);
            $id_pedido = $pdo->lastInsertId(); //Se guarda el último pedido


            // Insertar los productos en detalle_pedido


            $queryDetalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad_producto) VALUES (?, ?, ?)";
            $stmDetalle = $pdo->prepare($queryDetalle);
           
            //en cada iteración se inserta un producto del pedido en detalle_pedido usando el mismo id_pedido para todos
            foreach ($data->productos as $producto) {
                $stmDetalle->execute([$id_pedido, $producto->id_producto, $producto->cantidad]);
            }

           $pdo->commit(); //  Confirma la transacción
           echo json_encode(["message" => "Pedido registrado correctamente", "id_pedido" => $id_pedido]);


        } catch (PDOException $e) {
            $pdo->rollBack(); //  Revierte si hay error
            echo json_encode(["error" => "Error al registrar el pedido: " . $e->getMessage()]);
        }
      break;
       
       case "GET":  
            try{
           // $data = json_decode(file_get_contents("php://input"));
           
            if ($rol_empleado === "Administrador") {
              //Se construye la consulta antes del prepare()
              $query = "SELECT p.id_pedido, p.precio_total, p.fecha, p.num_empleado, p.estado,
                  e.nombre AS empleado, dp.id_producto, pr.nombre AS producto, dp.cantidad_producto
                  FROM pedidos p
                  JOIN empleados  e ON p.num_empleado = e.num_empleado
                  JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                  JOIN productos pr ON dp.id_producto = pr.id_producto "; //aquí no hay filtro
              $stm = $pdo->prepare($query);
              $stm->execute();
            }else{
              $query = "SELECT p.id_pedido, p.precio_total, p.fecha, p.num_empleado, p.estado,
                  e.nombre AS empleado, dp.id_producto, pr.nombre AS producto, dp.cantidad_producto
                  FROM pedidos p
                  JOIN empleados  e ON p.num_empleado = e.num_empleado
                  JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                  JOIN productos pr ON dp.id_producto = pr.id_producto
                  WHERE p.num_empleado = ? "; //ESte es el filtro es el num_empleado en la tabla pedido
               $stm = $pdo->prepare($query);
              $stm->execute([$num_empleado]); //solo si es camarero
              }
           
        
            $pedidos = $stm->fetchAll(PDO::FETCH_ASSOC); //Devolverá un array
            echo json_encode(["pedidos" => $pedidos]);
           
        }catch(PDOException $e){
           echo json_encode(["error" => "Error al obtener pedidos" . $e->getMessage()]);  
        }

        break;

      
   }


?>
