<?php

   function getData($id, $conn): array{

      $request = [];
      $sql = "SELECT s.id_solicitud, s.id_hue, s.id_tecnico, s.status, s.fecha_programada, s.motivo_cancelacion, s.tipo, s.rango, o.cantidad,
                  s.floracion, s.id_folio
                  FROM solicitudes s JOIN opciones_muestras o ON s.id_opciones_muestras = o.id_opciones_muestras 
                  WHERE s.id_tecnico = ? AND s.status = 'activa' AND s.tipo = 'exportacion'";
      $stm = $conn->prepare($sql);
      $stm->execute(array($id));
      
      while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
         array_push($request, $row);
      }

      $huertas = [];
      foreach($request as $value){
         $val = $value['id_hue'];
         if($val != null) array_push($huertas, getOrchardsByHue($val, $conn));
         else array_push($huertas, getOrchardsByFolio($value['id_folio'], $conn));
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
     return array(
         "solicitudes" => $request,
         "huertas" => $huertas,
         "juntas" => $juntas
     );
   }

   function getOrchardsByHue($hue, $conn){
      $sql = "SELECT t.*, t.id_hue as id FROM (SELECT * FROM huertas WHERE id_hue = ?) t";
      $stm = $conn->prepare($sql);
      $stm->execute(array($hue));
      $huerta = $stm->fetch(PDO::FETCH_ASSOC);
      unset($huerta["id_hue"]);
      unset($huerta["id_folio"]);
      return $huerta;
  }

   function getOrchardsByFolio($fol, $conn){
      $sql = "SELECT t.*, t.id_folio as id FROM (SELECT * FROM huertas_foliadas WHERE id_folio = ?) t";
      $stm = $conn->prepare($sql);
      $stm->execute(array($fol));
      $huerta = $stm->fetch(PDO::FETCH_ASSOC);
      unset($huerta["id_folio"]);
      unset($huerta["id_hue"]);
      return $huerta;
   }
?>