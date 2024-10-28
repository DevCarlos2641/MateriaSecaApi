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

    $data = json_decode(file_get_contents('php://input'));

    $pesos = $data->pesos;
    $muestra = $data->muestra;

    $sql = "INSERT INTO muestracampo VALUES(?, ?, ?, ?, ?, ?, ?)";
    $stm = $conn->prepare($sql);
    $stm->execute(array(
        $muestra->id_folio,
        $muestra->cantmuestra,
        $muestra->tipomuestreo,
        $muestra->fecha,
        $muestra->hora,
        $muestra->qr_image,
        6
        //$muestra->id_solicitud
    ));

    foreach($pesos as $i){
        $sql = "INSERT INTO pesoscampo VALUES(?, ?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->execute(array(
            0,
            $i->id_folio,
            $i->pesosmuestra,
            $i->coordenadas,
            "./imagenes/".$i->imagenes
        ));
    }

    $sql = "UPDATE solicitudes SET `status` = 'completed' WHERE id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($muestra->id_solicitud));


    echo json_encode("ok")

?>