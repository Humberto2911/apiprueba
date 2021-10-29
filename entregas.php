<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/entregas.class.php';

$_respuestas = new respuestas;
$_entregas = new entregas;


if($_SERVER['REQUEST_METHOD'] == "GET"){

    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaEntregas = $_entregas->listaEntregas($pagina);
        header("Content-Type: application/json");
        echo json_encode($listaEntregas);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $entregaid = $_GET['id'];
        $datosEntrega =  $_entregas->obtenerEntregas($entregaid);
        header("Content-Type: application/json");
        echo json_encode($datosEntrega);
        http_response_code(200);
    }else if(isset($_GET['idemisor'])){
        $idemisor = $_GET['idemisor'];
        $datosEntrega =  $_entregas->obtenerEntregasbyEmisor($idemisor);
        header("Content-Type: application/json");
        echo json_encode($datosEntrega);
        http_response_code(200);
    }
    
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_entregas->post($postBody);
    //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);
    
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
      //recibimos los datos enviados
      $postBody = file_get_contents("php://input");
      //enviamos datos al manejador
      $datosArray = $_entregas->put($postBody);
        //delvovemos una respuesta 
     header('Content-Type: application/json');
     if(isset($datosArray["result"]["error_id"])){
         $responseCode = $datosArray["result"]["error_id"];
         http_response_code($responseCode);
     }else{
         http_response_code(200);
     }
     echo json_encode($datosArray);

}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

        $headers = getallheaders();
        if(isset($headers["token"]) && isset($headers["entregaId"])){
            //recibimos los datos enviados por el header
            $send = [
                "token" => $headers["token"],
                "entregaId" =>$headers["entregaId"]
            ];
            $postBody = json_encode($send);
        }else{
            //recibimos los datos enviados
            $postBody = file_get_contents("php://input");
        }
        
        //enviamos datos al manejador
        $datosArray = $_entregas->delete($postBody);
        //delvovemos una respuesta 
        header('Content-Type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
       

}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}


?>