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
    $type = $_GET['tipo'];

    $sql = "SELECT s.id_solicitud, s.id_hue, s.id_tecnico, s.status, s.fecha_programada, s.motivo_cancelacion, s.tipo, s.rango, o.cantidad,
                    s.floracion, s.id_folio
                    FROM solicitudes s JOIN opciones_muestras o ON s.id_opciones_muestras = o.id_opciones_muestras 
                    WHERE s.id_solicitud = ? AND s.tipo = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($id, $type));
    $req = $stm->fetch(PDO::FETCH_ASSOC);

    if($req){
        if($req['status'] == "laboratorio"){
            $orchard;
            $val = $req['id_hue'];
            if($val != null) $orchard = getOrchardsByHue($val);
            else $orchard = getOrchardsByFolio($req['id_folio']);
            echo json_encode(array("orchard"=>$orchard, "request"=>$req));

        } else if($req["status"] == "activa" && $req["tipo"] == "nacional"){
            $orchard;
            $val = $req['id_hue'];
            if($val != null) $orchard = getOrchardsByHue($val);
            else $orchard = getOrchardsByFolio($req['id_folio']);
            echo json_encode(array("orchard"=>$orchard, "request"=>$req));
        } else echo http_response_code(404);

    } else echo http_response_code(404);

    function getOrchardsByHue($hue){
        global $conn;
        $sql = "SELECT t.*, t.id_hue as id FROM (SELECT * FROM huertas WHERE id_hue = ?) t";
        $stm = $conn->prepare($sql);
        $stm->execute(array($hue));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        unset($huerta["id_hue"]);
        unset($huerta["id_folio"]);
        return $huerta;
    }

    function getOrchardsByFolio($fol){
        global $conn;
        $sql = "SELECT t.*, t.id_folio as id FROM (SELECT * FROM huertas_foliadas WHERE id_folio = ?) t";
        $stm = $conn->prepare($sql);
        $stm->execute(array($fol));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        unset($huerta["id_folio"]);
        unset($huerta["id_hue"]);
        return $huerta;
    }

?>