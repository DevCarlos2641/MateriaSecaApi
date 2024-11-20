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
    $sql = "SELECT s.id_solicitud, s.id_hue, s.id_tecnico, s.status, s.fecha_programada, s.motivo_cancelacion, s.tipo, s.rango, o.cantidad 
                    FROM solicitudes s JOIN opciones_muestras o ON s.id_opciones_muestras = o.id_opciones_muestras 
                    WHERE s.id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id));
    $req = $stm->fetch(PDO::FETCH_ASSOC);

    if($req){
        if($req['status'] == "laboratorio"){
            $sql = "SELECT * FROM huertas WHERE id_hue = ?";    
            $stm = $conn->prepare($sql);
            $stm->execute(array($req['id_hue']));
            $orchard = $stm->fetch(PDO::FETCH_ASSOC);

            echo json_encode(array("orchard"=>$orchard, "request"=>$req));

        } else echo http_response_code(500);

    } else echo http_response_code(404);;

?>