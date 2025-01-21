<?php
    require_once('./connectividad.php');
    require_once('tecnico/requests.php');
    
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Verificar la conexión a la base de datos
    if ($conn->errorCode() !== "00000") {
        // Manejo del error de conexión aquí
        $errorInfo = $conn->errorInfo();
        die("Conexión fallida: " . implode(", ", $errorInfo));
    }

    $data = json_decode(file_get_contents('php://input'));

    $sql = "SELECT * FROM usuario WHERE correo = ?";
    $stm = $conn->prepare($sql);
    $stm->execute(array($data->email));
    $options = [];
    // Verificar si hay resultados
    if ($stm->rowCount() > 0) {
        $usuario = $stm->fetch(PDO::FETCH_ASSOC);

        if(!password_verify($data->password, $usuario['contraseña'])){
            $options = array('status' => 'error', 'usuario' => null);
            echo json_encode($options);
            die();
        }

        $idTipo = $usuario['id_tipo'];
        $idUser = $usuario['id_usuario'];
        if($idTipo == 2){
            //  get technician from DB and save in to  var $tecnico
            $sql = "SELECT * FROM tecnico WHERE id_usuario = ?";
            $stm = $conn->prepare($sql);
            $stm->execute(array($idUser));
            $tecnico = $stm->fetch(PDO::FETCH_ASSOC);

            // get request for technician from DB and save in to  var $solis
            $data = getData($tecnico['id_tecnico'], $conn);

            $options = array('status'=>'ok', 'usuario'=>$usuario, 
                        "tecnico"=>$tecnico, "solicitudes"=>$data['solicitudes'],
                        "huertas"=>$data['huertas'], "juntas"=>$data['juntas']);

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
?>