<?php
$host = '127.0.0.1';
$db = 'GestionPedidos';
// $user = 'usu_gestion';
// $pass = 'usu_gestion';

$user = 'root';
$pass = 'Nilo23Rio';
try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>
