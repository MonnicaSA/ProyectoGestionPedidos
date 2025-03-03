<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
session_start();

// Verificar autenticación
if (!isset($_SESSION['num_empleado'])) {
    echo json_encode(["error" => "No autenticado"]);
    exit;
}

// Obtener productos ordenados por tipo
$sql = "SELECT id_producto, nombre, tipo, precio_unitario FROM productos ORDER BY tipo";
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);
?>