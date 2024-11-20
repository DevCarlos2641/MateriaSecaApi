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

    $id = $_GET['id_solicitud'];

    $request = [];
    $sql = "SELECT s.id_solicitud, s.id_hue, s.id_tecnico, s.status, s.fecha_programada, s.motivo_cancelacion, s.tipo, s.rango, o.cantidad 
                    FROM solicitudes s JOIN opciones_muestras o ON s.id_opciones_muestras = o.id_opciones_muestras 
                    WHERE s.id_tecnico = ? AND status = 'activa'";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id));
    
    while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
        array_push($request, $row);
    }

    $huertas = [];
    foreach($request as $value){
        $hue = $value['id_hue'];
        $sql = "SELECT * FROM huertas WHERE id_hue = ?";
        $stm = $conn->prepare($sql);
        $stm->execute(array($hue));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        if(!(in_array($huerta, $huertas))){
            array_push($huertas, $huerta);
        }
    }
    
    $juntas = [];
    $ids = [];
    foreach($huertas as $h){
        $id = $h['idjuntalocal'];
        if(!in_array($id, $ids)){
            $ids[] = $id;
            
            $sql = "SELECT * FROM juntaslocales WHERE idjuntalocal = ?";
            $stm = $conn->prepare($sql);
            $stm->execute(array($id));
            $junta = $stm->fetch(PDO::FETCH_ASSOC);
            array_push($juntas, $junta);
        }
    }

    echo json_encode(
        array(
            "solicitudes" => $request,
            "huertas" => $huertas,
            "juntas" => $juntas
        )
    );
?>