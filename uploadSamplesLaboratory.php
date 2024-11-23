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
    $sample = $data->sample;
    $weights = $data->weights;
    $id_solicitud = $data->id_solicitud;
    $newFolio = $data->newFolio;

    if($newFolio != ""){
        $sample->id_folio = $newFolio;
        $sql = "INSERT INTO muestracampo VALUES(?, ?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->execute(array(
            $newFolio, null, $sample->fecha_final, null, $id_solicitud
        ));
    }

    $sql = "INSERT INTO muestralaboratorio VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stm = $conn->prepare($sql);
    $stm->execute(array(
        $sample->id_muestralaboratorio,
        $sample->id_folio,
        $sample->fecha_inicio,
        $sample->fecha_final,
        $sample->id_usuario,
        $sample->observaciones,
        $sample->rutaReporte,
        $sample->promedio_muestreo,
        $sample->desv_estandar,
        $sample->procentaje_cv
    ));

    $sql = "SELECT * FROM muestralaboratorio WHERE id_folio = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array(
        $sample->id_folio
    ));
    $sam = $stm->fetch(PDO::FETCH_ASSOC);
    $idM = $sam['id_muestralaboratorio'];

    foreach($weights as $i){
        $sql = "INSERT INTO pesoslaboratorio VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->execute(array(
            $i->id_pesolaboratorio,
            $idM,
            $sample->id_folio,
            $i->pesosmuestra,
            $i->pesopapel,
            $i->pesopapelpulpa,
            $i->pesomuestraseca,
            $i->pesomuestrahumeda,
            $i->pesonetomuestraseca,
            $i->porchumedad
        ));
    }

    $sql = "UPDATE solicitudes SET status = 'finalizado' WHERE id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id_solicitud));

    echo json_encode("ok");

    $conn = null;
    $result = null;

?>