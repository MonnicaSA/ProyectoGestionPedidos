<?php

session_set_cookie_params([
    'lifetime' => 3600,
    'path'  => '/',
    'secure' => false, //Cambiar a true si usas https
    'httponly' => true,

]);

session_start(); //inicia la sesión

//Configuración de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/errors.log');

require 'config.php';

$data = json_decode(file_get_contents("php://input"));

//Comprobamos si los campos existen
if(!isset($data->email) || !isset($data->contrasenia)){
  echo json_encode(["message" => "Faltan campos requeridos"]);
  exit;
}

//Se guarda los valores ingresados pore el usuario en otras variables
$email = $data->email;
$contrasenia = $data->contrasenia;

try{
  $query = "SELECT num_empleado, email, contrasenia FROM empleados WHERE email = ?"; 
  $stm = $pdo->prepare($query);
  $stm->execute([$email]);

  //Verificamos que exista el email proporcionado en el registro
  $user = $stm->fetch(PDO::FETCH_ASSOC); //$user es un array asociativo

  error_log($user["contrasenia"]);
  error_log($user["id"]);
   
  //Si email existe se guarda en el array $user en el campo email
  if($user)
   //Se obtiene la contrasenia
    $contraseniaEncontrada = $user["contrasenia"];
   //Si la contrasenia encontrada es igual a la contraseña ingresada
    if(password_verify( $contrasenia,  $contraseniaEncontrada)){
     $_SESSION["user_id"] = $user["num_empleado"]; //se crea la sesión
     echo json_encode(["numEmpleado" => $user['num_empleado'], 'message' => 'Inicio de sesión exitoso'] );
   } else{
       echo json_encode(["message" => "Contraseña incorrecta"] );
   }
}catch(PDOException $e){
      echo json_encode(["error" => "Usuario no encontrado "]);
}

?>
