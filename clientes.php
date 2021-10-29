<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/clientes.class.php';

$_respuestas = new respuestas;
$_clientes = new clientes;


if($_SERVER['REQUEST_METHOD'] == "GET"){

    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaClientes = $_clientes->listaClientes($pagina);
        header("Content-Type: application/json");
        echo json_encode($listaClientes);
        http_response_code(200);
    }else if(isset($_GET['id'])){
        $clienteid = $_GET['id'];
        $datosCliente = $_clientes->obtenerClientes($clienteid);
        header("Content-Type: application/json");
        echo json_encode($datosCliente);
        http_response_code(200);
    }else if(isset($_GET['nombre'])){
        $nombre = $_GET['nombre'];
        $datosCliente = $_clientes->obtenerClientesByNombre($nombre);
        header("Content-Type: application/json");
        echo json_encode($datosCliente);
        http_response_code(200);
    }else if(isset($_GET['identificacion'])){
        $identificacion = $_GET['identificacion'];
        $datosCliente = $_clientes->obtenerClientesIdentificacion($identificacion);
        header("Content-Type: application/json");
        echo json_encode($datosCliente);
        http_response_code(200);
    }else if(isset($_GET['correo'])){
        $correo = $_GET['correo'];
        $datosCliente = $_clientes->obtenerClientesCorreo($correo);
        header("Content-Type: application/json");
        echo json_encode($datosCliente);
        http_response_code(200);
    }
    
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados
    $postBody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_clientes->post($postBody);
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
      $datosArray = $_clientes->put($postBody);
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
        if(isset($headers["token"]) && isset($headers["clienteId"])){
            //recibimos los datos enviados por el header
            $send = [
                "token" => $headers["token"],
                "clienteId" =>$headers["clienteId"]
            ];
            $postBody = json_encode($send);
        }else{
            //recibimos los datos enviados
            $postBody = file_get_contents("php://input");
        }
        
        //enviamos datos al manejador
        $datosArray = $_clientes->delete($postBody);
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