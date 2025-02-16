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
  $query = "SELECT num_empleado, email, contrasenia, rol FROM empleados WHERE email = ?"; 
  $stm = $pdo->prepare($query);
  $stm->execute([$email]);

  //Verificamos que exista el email proporcionado en el registro
  $user = $stm->fetch(PDO::FETCH_ASSOC); //$user es un array asociativo

  error_log($user["contrasenia"]);
  error_log($user["num_empleado"]);
   
  //Si email existe se guarda en el array $user en el campo email
  if($user){
   //Se obtiene la contrasenia
    $contraseniaEncontrada = $user["contrasenia"];
   //Si la contrasenia encontrada es igual a la contraseña ingresada
    if(password_verify( $contrasenia,  $contraseniaEncontrada)){
      //Guardar la sesión
     $_SESSION["num_empleado"] = $user["num_empleado"]; //se crea la sesión
     $_SESSION["rol_empleado"] = $user["rol"];
     
     //json devuelve el mensaje al frontEnd
     echo json_encode(["numEmpleado" => $user['num_empleado'], "rolEmpleado" => $user["rol"], 'message' => 'Inicio de sesion exitoso'] );

   } else{
       echo json_encode(["message" => "contraseña incorrecta"]);
   }

  }else{
    echo json_encode(["message" => "Usuario no encontrado"]);
  }
}catch(PDOException $e){
   echo json_encode(["error" => "Usuario no encontrado"]);
}



