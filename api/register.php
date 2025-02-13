<?php
require 'config.php';

//Configuración de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');

$data = json_decode(file_get_contents("php://input"));

//Comprobamos los campos si existen
if(!isset($data->nombre) || !isset($data->email) || !isset($data->contrasenia) || !isset($data->rol)){
   var_dump($data);
  echo json_encode(["message" => "Faltan campos requeridos"]);
 
  exit;
} 
 

$nombre = $data->nombre;
$email = $data->email;
$contrasenia = $data->contrasenia;
$rol = $data->rol;

$query = "SELECT  COUNT(*) FROM empleados WHERE email = ?";
$stm = $pdo->prepare($query);
$stm->execute([$email]);
$emailExis = $stm->fetchColumn() > 0;

if($emailExis){
  echo json_encode(["message" => "El email ya está registrado"]);
  exit;
}else{
  
 //  error_log("Entrando en register ...");
  //Se hace un insert
  try{
     $query = "INSERT INTO empleados ( nombre, email, contrasenia, rol) VALUES ( ?, ?, ?, ?)";
     $stm = $pdo->prepare($query);
      //Hasheamos la contraseña antes de guardarla
  $hashContrasenia = password_hash($contrasenia,PASSWORD_DEFAULT);

     $stm->execute([$nombre, $email, $hashContrasenia, $rol]);
     echo json_encode(["message" => "Registro exitoso"]);

  }catch(PDOException $e){
    echo json_encode(["error" => "Las credenciales son incorrectas" . $e->getMessage() ]);
  }
}

?>
