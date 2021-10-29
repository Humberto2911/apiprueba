<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class entregas extends conexion {

    private $table = "entregas";
    private $entregaid = "";
    private $fechaEntrega = "";
    private $direccion = "";
    private $nombreReceptor = "";
    private $telefonoReceptor = "";
    private $idemisor = "";
    private $token = "";
 
//912bc00f049ac8464472020c5cd06759

    public function listaEntregas($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT EntregaId,FechaEntrega,Direccion,NombreReceptor,TelefonoReceptor FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerEntregas($id){
        $query = "SELECT * FROM " . $this->table . " WHERE EntregaId = '$id'";
        return parent::obtenerDatos($query);

    }

    public function obtenerEntregasbyEmisor($id){
        $query = "SELECT * FROM " . $this->table . " WHERE IdEmisor = '$id'";
        return parent::obtenerDatos($query);

    }

    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
                return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['fechaEntrega']) || !isset($datos['direccion']) 
                || !isset($datos['nombreReceptor']) ||  !isset($datos['telefonoReceptor'])){
                    return $_respuestas->error_400();
                }else{

                    $this->fechaEntrega = $datos['fechaEntrega'];
                    $this->direccion = $datos['direccion'];
                    $this->nombreReceptor = $datos['nombreReceptor'];
                    $this->telefonoReceptor = $datos['telefonoReceptor'];
                    $this->idemisor = $datos['idemisor'];
                    $resp = $this->insertarPaciente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "entregaId" => $resp
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


       

    }


    private function insertarPaciente(){
        $query = "INSERT INTO " . $this->table . " (FechaEntrega,Direccion,NombreReceptor,TelefonoReceptor,IdEmisor)
        values
        ('" . $this->fechaEntrega . "','" . $this->direccion . "','" . $this->nombreReceptor .  "','" . $this->telefonoReceptor  .  "','" . $this->idemisor."')"; 
        $resp = parent::nonQueryId($query);
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }
    
    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['entregaId'])){
                    return $_respuestas->error_400();
                }else{
                    $this->entregaid = $datos['entregaId'];
                    if(isset($datos['fechaEntrega'])) { $this->fechaEntrega = $datos['fechaEntrega']; }
                    if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    if(isset($datos['nombreReceptor'])) { $this->nombreReceptor = $datos['nombreReceptor']; }
                    if(isset($datos['telefonoReceptor'])) { $this->telefonoReceptor = $datos['telefonoReceptor']; }
                  
                      $resp = $this->modificarPaciente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "entregaId" => $this->entregaid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }


    }


    private function modificarPaciente(){
        $query = "UPDATE " . $this->table . " SET FechaEntrega ='" . $this->fechaEntrega . "',Direccion = '" . $this->direccion . 
        "', NombreReceptor = '" . $this->nombreReceptor . "', TelefonoReceptor = '" . $this->telefonoReceptor .
         "' WHERE EntregaId = '" . $this->entregaid . "'"; 
        $resp = parent::nonQuery($query);
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }


    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['entregaId'])){
                    return $_respuestas->error_400();
                }else{
                    $this->entregaid = $datos['entregaId'];
                    $resp = $this->eliminarPaciente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "entregaId" => $this->entregaid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }



     
    }


    private function eliminarPaciente(){
        $query = "DELETE FROM " . $this->table . " WHERE EntregaId= '" . $this->entregaid . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }


    private function buscarToken(){
        $query = "SELECT  TokenId,UsuarioId,Estado from usuarios_token WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }


    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }



}





?>