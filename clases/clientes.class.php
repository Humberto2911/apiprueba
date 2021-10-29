<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class clientes extends conexion {
   
    private $table = "clientes";
    private $clienteid = "";
    private $identificacion = "";
    private $nombre = "";
    private $primerApellido = "";
    private $segundoApellido = "";
    private $telefono = "";
    private $correo = "";
    
    
//912bc00f049ac8464472020c5cd06759

    public function listaClientes($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT ClienteId,Identificacion,Nombre,PrimerApellido,SegundoApellido,Telefono,Correo FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }


    public function obtenerClientes($id){
        $query = "SELECT * FROM " . $this->table . " WHERE ClienteId = '$id'";
        return parent::obtenerDatos($query);

    }

    public function obtenerClientesByNombre($nombre){
        $query = "SELECT * FROM " . $this->table . " WHERE Nombre = '$nombre'";
        return parent::obtenerDatos($query);  
        
    }

    public function obtenerClientesIdentificacion($identificacion){
        $query = "SELECT * FROM " . $this->table . " WHERE Identificacion = '$identificacion'";
        return parent::obtenerDatos($query);  
    }

    public function obtenerClientesCorreo($correo){
        $query = "SELECT * FROM " . $this->table . " WHERE Correo = '$correo'";
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

                if(!isset($datos['identificacion']) || !isset($datos['nombre']) || !isset($datos['correo'])){
                    return $_respuestas->error_400();
                    
                }else{

                    $this->identificacion = $datos['identificacion'];
                    $this->nombre = $datos['nombre'];
                    $this->primerApellido = $datos['primerApellido'];
                    $this->segundoApellido = $datos['segundoApellido'];
                    $this->correo = $datos['correo'];
                    if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    $resp = $this->insertarClientes();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "clienteId" => $resp
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


    private function insertarClientes(){ 
        $query = "INSERT INTO " . $this->table . " (Identificacion,Nombre,PrimerApellido,SegundoApellido,Telefono,Correo)
        values
        ('" . $this->identificacion . "','" . $this->nombre . "','". $this->primerApellido 
        . "','" . $this->segundoApellido . "','" . $this->telefono ."','" . $this->correo . "')"; 
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
                if(!isset($datos['clienteId'])){
                    return $_respuestas->error_400();
                }else{
                  
                    $this->clienteid = $datos['clienteId'];
                    if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                    if(isset($datos['primerApellido'])) { $this->primerApellido = $datos['primerApellido']; }
                    if(isset($datos['segundoApellido'])) { $this->segundoApellido = $datos['segundoApellido']; }
                    if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    if(isset($datos['correo'])) { $this->correo = $datos['correo']; }
                  
        
                    $resp = $this->modificarCliente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "clienteId" => $this->clienteid
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


    private function modificarCliente(){
        $query = "UPDATE " . $this->table . " SET Nombre ='" . $this->nombre . "', PrimerApellido = '" .$this->primerApellido .
        "', SegundoApellido = '" .$this->segundoApellido . "', Telefono = '" . $this->telefono .  "', Correo = '" . $this->correo .
         "' WHERE ClienteId = '" . $this->clienteid . "'"; 
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

                if(!isset($datos['clienteId'])){
                    return $_respuestas->error_400();
                }else{
                    $this->clienteid = $datos['clienteId'];
                    $resp = $this->eliminarCliente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "clienteId" => $this->clienteid
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


    private function eliminarCliente(){
        $query = "DELETE FROM " . $this->table . " WHERE ClienteId= '" . $this->clienteid . "'";
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