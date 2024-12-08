<?php
    //require_once('./connectividad.php');
    require_once('../DataBase/connectividad.php');
    require_once('requests.php');
    $conexion = new DB_Connect();
    $conn = $conexion->connect();

    // Verificar la conexión a la base de datos
    if ($conn->errorCode() !== "00000") {
        // Manejo del error de conexión aquí
        $errorInfo = $conn->errorInfo();
        die("Conexión fallida: " . implode(", ", $errorInfo));
    }

    $id = $_GET['id_tecnico'];
    $data = getData($id);


    echo json_encode($data);
?>