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

    $sql = "SELECT * FROM usuario WHERE correo = ? && contraseña = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($data->email, $data->password));
    $options = [];
    // Verificar si hay resultados
    if ($stm->rowCount() > 0) {
        $usuario = $stm->fetch(PDO::FETCH_ASSOC);
        $idTipo = $usuario['id_tipo'];
        $idUser = $usuario['id_usuario'];
        if($idTipo == 2){
            //  get technician from DB and save in to  var $tecnico
            $sql = "SELECT * FROM tecnico WHERE id_usuario = ?";
            $stm = $conn->prepare($sql);
            $stm->execute(array($idUser));
            $tecnico = $stm->fetch(PDO::FETCH_ASSOC);

            // get request for technician from DB and save in to  var $solis
            $idTec = $tecnico['id_tecnico'];
            $sql = "SELECT s.id_solicitud, s.id_hue, s.id_tecnico, s.status, s.fecha_programada, s.motivo_cancelacion, s.tipo, s.rango, o.cantidad,
                    s.floracion, s.id_folio
                    FROM solicitudes s JOIN opciones_muestras o ON s.id_opciones_muestras = o.id_opciones_muestras 
                    WHERE s.id_tecnico = ? AND status = 'activa'";
            $stm = $conn->prepare($sql);
            $stm->execute(array($idTec));

            $solis = [];
            while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
                array_push($solis, $row);
            }

            //  get huertas for request from DB and save in to var $huertas
            $huertas = [];
            foreach($solis as $value){
                $val = $value['id_hue'];
                if($val != null) array_push($huertas, getOrchardsByHue($val));
                else array_push($huertas, getOrchardsByFolio($value['id_folio']));
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

            $options = array('status'=>'ok', 'usuario'=>$usuario, 
                        "tecnico"=>$tecnico, "solicitudes"=>$solis,
                        "huertas"=>$huertas, "juntas"=>$juntas);

        }
        else if($idTipo == 3){
            $options = array('status'=>'ok', 'usuario'=>$usuario);
        }
        else 
            $options = array('status' => 'invalid', 'usuario' => null);
    } else {
        $options = array('status' => 'error', 'usuario' => null);
    }

    // Cerrar la conexión
    $conn = null;
    $result = null;

    // Devolver las opciones como respuesta (formato JSON)
    echo json_encode($options);

    function getOrchardsByHue($hue){
        global $conn;
        global $huertas;
        $sql = "SELECT t.*, t.id_hue as id FROM (SELECT * FROM huertas WHERE id_hue = ?) t";
        $stm = $conn->prepare($sql);
        $stm->execute(array($hue));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        unset($huerta["id_hue"]);
        unset($huerta["id_folio"]);
        if(!(in_array($huerta, $huertas))){
            return $huerta;
        }
    }

    function getOrchardsByFolio($fol){
        global $conn;
        global $huertas;
        $sql = "SELECT t.*, t.id_folio as id FROM (SELECT * FROM huertas_foliadas WHERE id_folio = ?) t";
        $stm = $conn->prepare($sql);
        $stm->execute(array($fol));
        $huerta = $stm->fetch(PDO::FETCH_ASSOC);
        unset($huerta["id_folio"]);
        unset($huerta["id_hue"]);
        if(!(in_array($huerta, $huertas))){
            return $huerta;
        }
    }

?>