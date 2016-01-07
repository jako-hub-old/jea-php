<?php
/**
 * Este es el archivo de alias en el sistema, si se requiere un alias que esté 
 * disponible en todo el sistema se debe registrar aquí, recuerda que para definir
 * un alias es necesario anteponer el signo de admiración
 * 
 * @package sistema.utilidades
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2015, jakop
 */

$rutaBase = Sistema::getUbicacion();

return array(
    '!sistema' => $rutaBase,
    '!web' => $rutaBase.DS.'web',
    '!base' => $rutaBase.DS.'base',
    '!siscoms' => $rutaBase.DS.'web'.DS.'coms',
);
