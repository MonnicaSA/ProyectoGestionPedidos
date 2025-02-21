<?php
header('Content-Type: application/json'); // Asegura que la respuesta sea JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, // Cambiar a true si usas HTTPS
    'httponly' => true,
]);

session_start();

require 'config.php';

// Leer datos de la petición
$data = json_decode(file_get_contents("php://input"), true);

// Validar que se enviaron email y contraseña
if (!isset($data['email']) || !isset($data['contrasenia'])) {
    echo json_encode(["error" => "Faltan campos requeridos"]);
    exit;
}

$email = $data['email'];
$contrasenia = $data['contrasenia'];

try {
    // Buscar usuario en la base de datos
    $query = "SELECT num_empleado, email, contrasenia, rol FROM empleados WHERE email = ?"; 
    $stm = $pdo->prepare($query);
    $stm->execute([$email]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario existe
    if (!$user) {
        echo json_encode(["error" => "Usuario no encontrado"]);
        exit;
    }

    // Verificar contraseña
    if (!password_verify($contrasenia, $user["contrasenia"])) {
        echo json_encode(["error" => "Contraseña incorrecta"]);
        exit;
    }

    // Iniciar sesión
    $_SESSION["num_empleado"] = $user["num_empleado"];
    $_SESSION["rol_empleado"] = $user["rol"];

    echo json_encode([
        "numEmpleado" => $user["num_empleado"],
        "rolEmpleado" => $user["rol"],
        "message" => "Inicio de sesión exitoso"
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la base de datos"]);
}
?>