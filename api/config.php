
<?php
$host = '127.0.0.1';
$db = 'GestionPedidos';
$user = 'usu_gestion';
$pass = 'usu_gestion';


try {
    $pdo = new PDO("mysql:host=$host; dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){
echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);

}
?>
