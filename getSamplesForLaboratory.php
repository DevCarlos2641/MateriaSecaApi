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

    $sql = "SELECT * FROM muestracampo";
    $stm = $conn->prepare($sql);
    $stm->execute();
    if ($stm->rowCount() > 0) {
        echo json_encode("chi");
    }
?>