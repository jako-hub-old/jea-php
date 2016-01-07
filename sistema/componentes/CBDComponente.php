<?php
/**
 * Esta clase es el componente que se usará para el manejo de bases de datos
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 * @property CControladorBaseDeDatos $controlador Esta es la instancia del controlador de base de datos cargado
 */
class CBDComponente extends CComponenteAplicacion{
    private $controlador;
    
    public function __construct($config) {
        $this->cargarControlador($config);
    }
    
    /**
     * Esta función carga el controlador requerido en la configuración
     * @param array $config configuración de la base de datos
     * @throws CExAplicacion
     */
    private function cargarControlador($config){
        if(!isset($config['driver'])){
            throw new CExAplicacion("No está definido el controlador en la configuración");
        }
        $ctrl = $config['driver'];
        $funcion = 'cargar' . ucfirst(strtolower($ctrl));
        if(!method_exists($this, $ctrl)){
            call_user_func(array($this, $funcion), $config);
        }else{
            throw new CExAplicacion("No está definido el método para cargar ese controlador");
        }
    }
    
    /******************************************************
     * Funciones para cargar los controladores soportados *
     ******************************************************/
    
    /**
     * Esta función carga e inicializa el controlador para mysql
     * @param array $config
     */
    private function cargarMysql($config){
        CConectorMySql::iniciar($config);
        $this->controlador = new CMySqlDriver();
        # Si se definió un prefijo lo seteamos de inmediato en el controlador
        if(isset($config['prefijo'])){
            $this->controlador->prefijo = $config['prefijo'];
        }
    }
    
    /*********************
     * funciones magicas *
     *********************/
    public function __get($nombre){
        if(property_exists($this, $nombre)){
            return $this->$nombre;
        }
    }    
    
    public function iniciar() {}
}