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

    $sql = "INSERT INTO muestracampo VALUES(?, ?, ?, ?, ?)";
    $stm = $conn->prepare($sql);
    $stm->execute(array(
        $muestra->id_folio,
        $muestra->tipomuestreo,
        $muestra->fecha,
        $muestra->qr_image,
        $muestra->id_solicitud
    ));

    foreach($pesos as $i){
        $nameImg = $i->imagenes;
        //  id-hue-nameImage.jpg
        $id_thec = explode('-', $nameImg)[0];
        $hue = explode('-', $nameImg)[1];
        $name = preg_replace('/^[^-]+-[^-]+-/', '', $nameImg);
        $sql = "INSERT INTO pesoscampo VALUES(?, ?, ?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        $stm->execute(array(
            0,
            $i->id_folio,
            $i->pesosmuestra,
            $i->coordenadas,
            '/images/'.$id_thec.'/'.$hue.'/'.$name,
            $i->observaciones
        ));
    }
    
    $sql = "UPDATE solicitudes SET status = 'laboratorio' WHERE id_solicitud = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($muestra->id_solicitud));

    echo json_encode("ok")

?>