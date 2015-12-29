<?php
/**
 * Excepción que se arrojará cuando ocurra un error de aplicación
 * @package sistema.excepciones
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */

class CExAplicacion extends Exception{
    public $titulo = "Error en la aplicación";
    public function __construct($message, $code, $previous) {
        parent::__construct($message, $code, $previous);
    }
}
