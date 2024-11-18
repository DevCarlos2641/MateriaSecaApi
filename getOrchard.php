<?php

    //require_once('./connectividad.php');
    require_once('../DataBase/connectividad.php');
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Verificar la conexión a la base de datos
    if ($conn->errorCode() !== "00000") {
        // Manejo del error de conexión aquí
        $errorInfo = $conn->errorInfo();
        die("Conexión fallida: " . implode(", ", $errorInfo));
    }

    $folio = $_GET['id_solicitud'];

    $id = explode('-', $folio)[0];
    $sql = "SELECT * FROM solicitudes WHERE id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id));
    $req = $stm->fetch(PDO::FETCH_ASSOC);

    if($req){
        if($req['status'] == "laboratorio"){
            $sql = "SELECT * FROM huertas WHERE id_hue = ?";    
            $stm = $conn->prepare($sql);
            $stm->execute(array($req['id_hue']));
            $orchard = $stm->fetch(PDO::FETCH_ASSOC);
            echo json_encode($orchard);
        } else echo http_response_code(500);

    } else echo http_response_code(404);;

?>