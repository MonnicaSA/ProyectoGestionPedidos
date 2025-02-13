<?php
  session_start(); //Recupera la sesión
  session_destroy(); //Y la destruye
  echo json_encode(["message" => "Sesión cerrada"]);
?>