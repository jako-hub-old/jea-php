<?php
/**
 * Esta clase representa al sistema que contiene la aplicación, ella inicializa
 * toda la lógica básica que necesita una aplicación
 * 
 * @package sistema
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2015, jakop
 */
final class Sistema {
    /**
     * array que contiene todos los alias registrados en el sistema
     * @var array 
     */
    private static $alias;
    /**
     * Instancia de la aplicación
     * @var CAplicacionWeb 
     */
    private static $apliacion = null;
    /**
     * Ubicación en el servidor del sistema
     * @var string 
     */
    private static $rutaSistema;
    /**
     * Versión actual del sistema
     * @var string 
     */
    private static $version = '1.0.1';
    
    private function __construct() {}
    
    /**
     * Esta función retorna una instancia del sistema, la única instancia
     * que se puede crear
     * 
     * @staticvar \Sistema $instanciaSistema
     * @param string $rutaConfiguracion Ruta de donde se cargarán las configuraciones de la aplicación
     * @return \Sistema
     */
    public static function crearAplicacion($rutaConfiguracion){        
        self::$rutaSistema = realpath(__DIR__);
        self::cargarGlobales();
        self::cargarAlias();
        self::importar('!sistema.web.CAplicacionWeb');        
        self::$apliacion = CAplicacionWeb::getInstancia($rutaConfiguracion);
        self::$apliacion->inicializar();
        return self::$apliacion;
    } 
    
    /**
     * Esta función devuelve la única instancia de la aplicación en el sistema
     * @return CAplicacionWeb
     */
    public static function apl(){
        return self::$apliacion;
    }    
    
    /**
     * Esta función carga el archivo de globales del sistema
     * @throws Exception Si no se existe la ruta de las globales
     */
    private static function cargarGlobales(){
        $rutaGlobales = realpath(self::$rutaSistema.'/utilidades/Globales.php');
        if(!file_exists($rutaGlobales)){
            throw new Exception('Error al intentar cargar las globales del sistema');
        }
        require_once $rutaGlobales;
    }
    
    /**
     * Esta función carga el archivo que contiene los alias del sistema y asigna 
     * los alias guardados allí a la variable alias del sistema
     * @throws Exception si no existe la ruta de alias del sistema
     */
    private static function cargarAlias(){
        $rutaAlias = realpath(self::$rutaSistema.'/utilidades/Alias.php');
        if(!file_exists($rutaAlias)){
            throw new Exception("Error al incluir los alias del sistema");
        }
        self::$alias = include $rutaAlias;
    }
    
    /**
     * Esta función retorna la ruta de un alias si este está definido en el array de alias
     * @param string $nombre
     * @return mixed string con la ruta del alias si lo encuentra, false si no lo encuentra
     */
    public static function getAlias($nombre){
        return isset(self::$alias[$nombre])? 
            self::$alias[$nombre] : false;
    }
    /**
     * Esta función retorna un array con los alias registrados en el sistema
     * @return array
     */
    public static function getAliasTodos(){
        return self::$alias;
    }
    /**
     * Esta función permite registrar nuevos alias en la aplicación
     * @param array $alias el formato del array debe ser array('alias'=>'ruta', 'alias2'=>'ruta')
     */
    public static function setAlias($alias = []){
        foreach ($alias AS $key=>$value){
            self::$alias[$key] = $value;
        }
    }
    /**
     * Esta función retorna la ubicación del sistema
     * @return string
     */
    public static function getUbicacion(){
        return self::$rutaSistema;
    }
    /**
     * Esta función convierte una ruta tipo java en una ruta real del servidor
     * @param string $ruta la ruta debe tener formato java, como sistema.web.clase puede valerse de alias
     *                      para construir la ruta que desea
     *                      <ul>
     *                          <li>sistema: ruta base del sistema</li>
     *                          <li>web: ruta a la carpeta web</li>
     *                          <li>base: ruta a la carpeta base</li>
     *                          <li>aplicacion: ruta base a la aplicación</li>
     *                      </ul>
     * @return string
     */
    public static function resolverRuta($ruta, $conClase = false){
        $partes = explode('.',$ruta);
        $nombreClase = $partes[count($partes) - 1];
        $rutaReal = isset(self::$alias[$partes[0]])? self::$alias[$partes[0]] : $partes[0];
        for($i = 1; $i < count($partes) - 1; $i ++){ $rutaReal .= DS.$partes[$i]; }
        return $rutaReal.DS.$nombreClase.($conClase? '.php' : '');
    }
    
    /**
     * Esta función valida si una ruta existe
     * @param string $ruta
     * @param boolean $conAlias
     * @return boolean
     */
    public static function existeRuta($ruta, $conAlias = true){
        return $conAlias? file_exists(self::resolverRuta($ruta)) : 
                file_exists($ruta);
    }
    
    /**
     * Esta función importa una clase o archivo
     * @param string $ruta
     * @param boolean $usandoAlias
     * @return boolean true si se importa el archivo, false si no
     */
    public static function importar($ruta, $usandoAlias = true){
        if($usandoAlias){
            $ruta = self::resolverRuta($ruta, true);
        }

        if(!file_exists($ruta)){
            return false;
        }
        
        return (include_once $ruta) === 1;
    }
    
    /**
     * Esta función finaliza la ejecución de todo el sistema
     * @param type $estado
     */
    public static function fin(){
        exit();
    }
    
    /**
     * Esta función retorna la versión actual del sistema
     * @return string
     */
    public static function v(){
        return self::$version;
    }
}