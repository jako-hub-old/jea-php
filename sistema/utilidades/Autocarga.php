<?php
/**
 * Este es el archivo encargado de manejar la autocarga del sistema
 * @package sistema.utilidades
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */


/**
 * Esta función inspecciona todos los archivos de un directorio (usa recursividad)
 * y retorna un mapa con todos los archivos encontrados allí, donde la clave
 * es el nombre del archivo y el valor la url donde este se encuentra
 * @param string $ruta
 * @param array $excluciones
 * @return string[]
 */
function __inspeccionarSistema($ruta, $excluciones = []){
    $archivos = [];
    $directorios = scandir($ruta);
    foreach($directorios AS $archivo){
        // verificamos que el archivo que tenemos no esté en las excluciones
        if(in_array($archivo, $excluciones) || in_array($ruta.DS.$archivo, $excluciones)){
            continue;
        }
        
        if(is_file($ruta.DS.$archivo)){
            $archivos[str_replace('.php', '', $archivo)] = $ruta.DS.$archivo;
        }else if(($archivo !== '..' && $archivo !== '.') && is_dir($ruta.DS.$archivo)){
            // si el archivo es un directorio, recorremos los archivos que estan dentro
            // del directorio también
            $otrosArchivos = __inspeccionarSistema($ruta.DS.$archivo, $excluciones);
            $archivos = array_merge($archivos, $otrosArchivos);
        }
    }
    return $archivos;
}

function __autoload($_clase){
    // el mapa del sitio solo se construye una vez, de ahí en adelante se 
    // almacena en constantes que posteriormente son explotadas para volver a 
    // armar el mapa
    if(!defined('__MAPA_CLAVES__') && !defined('__MAPA_VALORES__')){
        $rutaBase = Sistema::getUbicacion();
        $excluciones = require_once 'Excluciones.php';                
        $mapaSitio = __inspeccionarSistema($rutaBase, $excluciones);
        $otrasImportaciones = [];
        if(defined('__IMPORTACIONES__')){
            $otrasImportaciones = explode(';', __IMPORTACIONES__);
        }
        foreach($otrasImportaciones AS $imp){
            $otrosArchivos = __inspeccionarSistema(Sistema::resolverRuta($imp));
            $mapaSitio = array_merge($mapaSitio, $otrosArchivos);
        }        
        define('__MAPA_CLAVES__', implode(';', array_keys($mapaSitio)));
        define('__MAPA_VALORES__', implode(';', $mapaSitio));        
    }else{ $mapaSitio = array_combine(explode(';', __MAPA_CLAVES__), explode(';', __MAPA_VALORES__)); }    
    
    // si existe la clase en el mapa la incluimos
    if(isset($mapaSitio[$_clase])){
        require_once $mapaSitio[$_clase];
    }else{
        throw new Exception("La clase '$_clase'  no se encuentra definida");
    }    
}
