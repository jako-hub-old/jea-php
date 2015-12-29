<?php
/**
 * Excepción que se arrojará cuando ocurra un error de sistema
 * @package sistema.excepciones
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CExSistema extends Exception{    
    public $titulo = "Error en el sistema";
    public function __construct($message = "", $code = null, $previous = null) {        
        parent::__construct($message, $code, $previous);
    }
}
