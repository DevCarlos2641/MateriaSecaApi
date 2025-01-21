<?php
   
   require_once('../connectividad.php');
   
   $conexion = new DB_Connect();
   $conn = $conexion->connect();

   // Verificar la conexión a la base de datos
   if ($conn->errorCode() !== "00000") {
      // Manejo del error de conexión aquí
      $errorInfo = $conn->errorInfo();
      die("Conexión fallida: " . implode(", ", $errorInfo));
   }

   $idJunta = $_GET["idjuntalocal"];

   $query = "SELECT * FROM basculas WHERE idJuntalocal = ?";
   $stm = $conn->prepare( $query );
   $stm->execute(array($idJunta));
   $basculas = $stm->fetchAll(PDO::FETCH_ASSOC);

   $query = "SELECT * FROM microondas WHERE idJuntalocal = ?";
   $stm = $conn->prepare( $query );
   $stm->execute(array($idJunta));
   $micro = $stm->fetchAll(PDO::FETCH_ASSOC);

   echo json_encode(array(
      "bascula" => $basculas,
      "microondas"=> $micro
   ));

?>