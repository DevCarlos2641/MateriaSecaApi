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

    $data = json_decode(file_get_contents('php://input'));

    $id = $data->id_solicitud;
    $motivo = $data->txt;

    date_default_timezone_set("America/Mexico_City");
    $fechaActual = date("Y/m/d");
    $sql = "UPDATE solicitudes SET motivo_cancelacion = ?, status = 'cancelada', fecha_fin = ? WHERE id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($motivo, $fechaActual, $id));

    echo json_encode("OK")

?>