<?php

    $target_dir = './images/';
    
    if (isset($_FILES['images'])) {

        $files = $_FILES['images'];

        foreach ($files['name'] as $key => $name) {
            $tmp_name = $files['tmp_name'][$key];
            $size = $files['size'][$key];
            $error = $files['error'][$key];

            // Verificar que no haya errores y que el archivo sea válido
            if ($error === UPLOAD_ERR_OK) {
                // Procesar el archivo, por ejemplo, moverlo a un directorio
                $filePath = $target_dir . basename($name);

                if (!move_uploaded_file($tmp_name, $filePath))
                    echo "Archivo $name subido exitosamente.\n";
            }
        }
        echo json_encode("ok");
    } else {
        echo json_encode("No se han subido archivos.");
    }
?>