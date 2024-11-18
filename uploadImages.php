<?php

    if (isset($_FILES['images'])) {

        $files = $_FILES['images'];

        $nameF = $files['name'][0];
        $id = explode('-', $nameF)[0];
        $hue = explode('-', $nameF)[1];
        verifiedDirImages();
        verifiedDirId($id);
        verifiedDirHue($id, $hue);

        foreach ($files['name'] as $key => $name) {
            $target_dir = './images/';
            $tmp_name = $files['tmp_name'][$key];
            $size = $files['size'][$key];
            $error = $files['error'][$key];

            // Verificar que no haya errores y que el archivo sea válido
            if ($error === UPLOAD_ERR_OK) {
                // Procesar el archivo, por ejemplo, moverlo a un directorio

                $target_dir = $target_dir.$id.'/'.$hue.'/';
                $aux = $id.'-'.$hue.'-';
                $name = str_replace($aux, "", $name);

                $filePath = $target_dir . basename($name);

                if (!move_uploaded_file($tmp_name, $filePath)) {
                    echo json_encode("Error al mover el archivo $name");
                }
            }
        }
        echo json_encode("ok");
    } else {
        echo json_encode("No se han subido archivos.");
    }

    function verifiedDirId($id){
        $dir = './images/'.$id;
        if (!is_dir($dir))
            mkdir($dir, 0777, true);
        return true;
    }

    function verifiedDirHue($id, $hue){
        $dir = './images/'.$id.'/'.$hue;
        if (!is_dir($dir))
            mkdir($dir, 0777, true);
        return true;
    }

    function verifiedDirImages(){
        $dir = './images';
        if (!is_dir($dir))
            mkdir($dir, 0777, true);
        return true;
    }

?>