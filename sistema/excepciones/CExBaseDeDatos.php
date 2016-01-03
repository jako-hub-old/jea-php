<?php
/**
 * Excepción que se arrojará cuando ocurra un error de base de datos
 * @package sistema.excepciones
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */

class CExBaseDeDatos extends Exception{
    public $titulo = "Error de base de datos";
    public function __construct($message, $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
