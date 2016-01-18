<?php
/**
 * Esta clase es la super clase de todos los módulos de la aplicación
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2016, jakop
 * 
 * @property CControlador $controlador Controlador invocado en el módulo;
 */
abstract class CModulo extends CComponenteAplicacion{
    /**
     * Ubicación del módulo
     * @var string
     */
    private $ruta;
    /**
     * Ubicación de la carpeta de vistas del módulo
     * @var string 
     */
    private $rutaVistas;
    /**
     * Ubicación de la carpeta de controladores del módulo
     * @var string 
     */
    private $rutaControladores;
    /**
     * Instancia del controlador del módulo
     * @var CControlador 
     */
    private $controlador;
    /**
     * controlador por defecto del módulo
     * @var string
     */
    protected $_controladorPorDefecto = 'principal';
    /**
     *Acción a invocar por defecto en el controlador
     * @var string 
     */
    protected $_accionPorDefecto = 'inicio';
    
    /**
     * Plantilla a cargar por defecto en el módulo
     * @var string 
     */
    protected $_plantilla = 'basica';
    
    public function __construct($id, $ruta) {
        # solo las variables seteables tendrán guíon bajo
        $this->c = '_';
        $this->ID = $id;
        $this->ruta = $ruta;
        $this->inicializar();
    }
    
    public function __get($nombre){
        $nombre = "get" . ucfirst($nombre);
        if(method_exists($this, $nombre)){
            return $this->$nombre();
        }
    }
    
    /**
     * Esta función inicializa los componentes básicos del módulo
     */
    public function inicializar(){
        $this->rutaVistas = $this->ruta.DS.'vistas';
        $this->rutaControladores = $this->ruta.DS.'controladores';
    }
    
    /**
     * Esta función inicializa en controlador invocado en el módulo
     * antes de que el módulo inicie
     */
    public function antesDeIniciar() {
        $this->controlador->plantilla = $this->_plantilla;
        $this->controlador->inicializar();
        $this->controlador->antesDeIniciar();
    }
    
    /**
     * Esta función inicia el módulo
     */
    public function iniciar() {
        $this->controlador->iniciar();
    }
    
    /**
     * Esta función permite cargar el módulo que se invoca
     * @param string $nombre
     * @param string $accion
     * @throws CExAplicacion
     */
    public function setControlador($nombre, $accion){
        if(!file_exists($this->rutaControladores) && !is_dir($this->rutaControladores)){
            throw new CExAplicacion("No existe la ruta de controladores del módulo cargado");
        }
        $nControlador = $nombre !== null? $nombre : $this->_controladorPorDefecto;
        $nAccion = $accion !== null? $accion : $this->_accionPorDefecto;
        
        $this->controlador = $this->cargarControlador($nControlador, $nAccion);
    }
    
    /**
     * Esta función carga el controlador destinado para el módulo
     * @param string $nombre
     * @param string $accion
     * @return CControlador
     * @throws CExAplicacion
     */
    private function cargarControlador($nombre, $accion){
        #armamos el nombre que debe tener la clase y el archivo del controlador
        $nombreClase = "Ctrl".  ucfirst($nombre);
        #armamos la rutta de los controladores
        $ruta = $this->rutaControladores.DS."$nombreClase.php";
        
        if(!Sistema::importar($ruta, false)){
            throw new CExAplicacion("No exsite el controlador <b>$nombreClase</b> para el módulo <b>$this->ID</b>");
        }
        
        $controlador = new $nombreClase($nombre, $accion);
        #el controlador cargado debe ser instancia de CControlador
        if(!$controlador instanceof CControlador){
            throw new CExAplicacion("El controlador cargado no es valido");
        }
        #pasamos la ruta de las vistas del módulo al controlador
        $controlador->setRutaVistas($this->rutaVistas);
        $controlador->perteneceAModulo(true);
        return $controlador;
    }
    
    /**
     * Esta función carga un módulo por su nombre
     * @param string $nombre
     * @return CModulo
     */
    public static function cargarModulo($nombre){
        $configuracion  = self::validarModulo($nombre);
        return self::obtenerInstancia($configuracion);        
    }
    
    /**
     * Esta función genera una instancia del módulo seleccionado
     * @param array $config
     * @return CModulo Instancia
     */
    private static function obtenerInstancia($config){
        $clase = $config['clase'];
        $instancia = new $clase($config['nombre'], $config['ruta']);
        # la instancia cargada debe ser hija de CModulo
        if(!$instancia instanceof CModulo){
            throw new CExAplicacion("El módulo cargado no es valido, debe ser una instancia de CModulo");
        }
        $instancia->asignarAtributos($config['config']);
        return $instancia;
    }
    
    /**
     * Esta función valida que un módulo esté registrado y que exista todo
     * lo necesario para que dicho módulo pueda ser creado
     * @param string $nombre
     * @return array la configuración necesaria para instanciar un módulo
     * @throws CExAplicacion
     */
    private static function validarModulo($nombre){
        # obtenemos la configuración de módulos
        $modulos = Sistema::apl()->getConfiguracion('modulos');
        # si no existe la configuración de modulos
        if($modulos === false){ throw new CExAplicacion("No hay módulos registrados"); }
        # si no está registrado el módulo
        if(!isset($modulos[$nombre])){ throw new CExAplicacion("El módulo solicitado no está registrado '$nombre'"); }
        $configuracion = $modulos[$nombre];
        $clase = $configuracion['clase'];
        $ruta = Sistema::resolverRuta($configuracion['ruta']);
        # archivo que se importará
        $archivo = Sistema::resolverRuta($configuracion['ruta'].".$clase", true);
        
        # si no existe el archivo que se importará
        if(!file_exists($archivo)){ throw new CExAplicacion("No existe el archivo del módulo '$nombre'"); }        
        Sistema::importar($archivo, false);
        # removemos la clase y la ruta, lo que quede, son variables para asignar a la instancia
        # del modulo
        unset($configuracion['clase']);
        unset($configuracion['ruta']);
        
        #retornamos la configuración necesaria para instanciar un módulo
        return array(
            'nombre' => $nombre,
            'ruta' => $ruta,
            'clase' => $clase,
            'config' => $configuracion
        );
    }
    
    /**
     * Esta función retorna el controlador invocado en el módulo
     * @return CControlador
     */
    public function getControlador(){
        return $this->controlador;
    }
    
    /**
     * Esta función retorna la ruta donde se encuentra alojado el módulo
     * @return string
     */
    public function getRuta(){
        return $this->ruta;
    }
}
