<?php
/**
 * Esta clase se encarga de manejar los errores
 * @package manejadores
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CMError {
    private $error;
    private $excepcion;
    
    public function __construct() {}
    
    public function tratarError($no, $mensaje, $archivo, $linea){
        new CExError($no, $mensaje, $archivo, $linea);
        Sistema::fin();
    }
    
    public function tratarExcepcion(Exception $e){
        // la clase CException ya se encarga de tomar los
        // datos necesarios de la excepción arrojada y mostrarlos
        new CExcepcion($e);
        Sistema::fin();
    }
    
}
