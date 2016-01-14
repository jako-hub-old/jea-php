<?php
/**
 * Esta clase será la plantilla de todos los controladores de la aplicación
 * contiene los elementos necesarios para que un controlador cumpla con su función
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.3
 * @copyright (c) 2015, 2015
 * 
 * @property string $ID ID del controlador invocado
 */
abstract class CControlador extends CComponenteAplicacion{
    
    /**
     * Instancia de la ación llamada para el controlador
     * @var CAccion 
     */
    protected $accion;
    /**
     * contenido generado al mostrar una vista
     * @var string 
     */
    protected $contenido;
    /**
     * Nombre de la plantilla a usar para mostrar vistas
     * @var string 
     */
    public $plantilla = 'basica';
    /**
     * Titulo de la pagína actual
     * @var string 
     */
    protected $tituloPagina;
    /**
     * Ruta de las vistas de este controlador
     * @var string 
     */
    protected $rutaVistas;
    /**
     * Nombre de la acción que fue invocada
     * @var string 
     */
    private $nombreAccion;
    /**
     * Nombre de la acción que se invocará por defecto
     * @var string 
     */
    protected $accionPorDefecto = null;
    /**
     * Indica si el controlador pertenece a un módulo
     * @var boolean 
     */
    private $perteneceAModulo = false;
    /**
     * Representación de la variable global $_POST, esta variable ya viene
     * filtrada con filter_input_array
     * @var array 
     */
    protected $_p;
    /**
     * Representación de la variable global $_GET, esta variable ya viene
     * filtrada con filter_input_array
     * @var array 
     */
    protected $_g;
    

    public function __construct($ID, $accion) {
        $this->ID = $ID;
        $this->nombreAccion = $accion;
        $this->rutaVistas = Sistema::resolverRuta('!aplicacion.vistas');
        $this->tituloPagina = ucwords($ID . ' - ' . $accion);
    }
    
    /**
     * Esta función es ejecutada antes de iniciar el controlador y sus componentes base
     * @throws CExAplicacion
     */
    public function antesDeIniciar() {
        # validamos si la acción invocada existe
        $accion = $this->cargarAccion($this->nombreAccion);
        
        # validamos si hay un acción por defecto para cargar
        $porDefecto = $this->accionPorDefecto !== null && 
                method_exists($this, 'accion'.  ucfirst($this->accionPorDefecto));
        if(!$accion && !$porDefecto){
            # se debe sobreescribir este comportamiento
            throw new CExAplicacion("La acción solicitada no está creada ($this->nombreAccion)");
        } else if(!$accion && $porDefecto){
            $this->cargarAccion($this->accionPorDefecto);
        }
    }
    
    /**
     * Se puede usar para inicializar valores del controlador, o escribir lógica
     * para que se ejecute antes de llamar cualquier acción del controlador
     */
    public function inicializar(){}
    
    /**
     * Esta función inicia el controlador
     */
    public function iniciar() {
        # obtenemos los parametros en la url
        $parametros = filter_input_array(INPUT_GET);
        
        # removemos el parametro de la ruta
        unset($parametros['r']);
        
        # seteamos de una vez $_GET y $_POST para el controlador
        $this->_g = $parametros;
        $this->_p = filter_input_array(INPUT_POST);
        if($this->_p == null){ $this->_p = []; }
        
        # validamos si hay parámetros para agregar a la función
        # NOTA: solo se agregará un parámetro a la función invocada
        if(count($parametros) > 0){
            $parametros = array_values($parametros);
            call_user_func(array($this, $this->accion->getFn()), $parametros[0]);
        }else{
            call_user_func(array($this, $this->accion->getFn()));
        }
    }
    
    /**
     * Esta función permite redireccionar de una acción a otra o de un controlador
     * a otro
     * @param string $ruta
     * @param array $parametros
     */
    public function redireccionar($ruta, $parametros = []){
        $partes = explode('/', $ruta);
        // si partes es igual a uno quiere decir que se esta invocando una acción del mismo controlador
        if(count($partes) === 1 && !$this->perteneceAModulo){
            $ruta = $this->ID.'/'.$ruta;
        } else if(count($partes) === 1 && $this->perteneceAModulo){
            $ruta = Sistema::apl()->modulo->ID."/".$this->ID.'/'.$ruta;
        }
        $url = Sistema::apl()->crearUrl(array_merge(array($ruta), $parametros));
        header("location:".$url);
        # tenemos que finalizar la ejecución de la aplicación
        Sistema::fin();
    }
    
    /**
     * Esta función permite mostrar una vista
     * @param string $vista
     * @param array $parametros
     */
    public function mostrarVista($vista = '', $parametros = []){
        $rutaVista = $this->validarVista(lcfirst($this->ID).DS.$vista);
        $this->contenido = $this->obtenerHtmlDeArchivo($rutaVista, $parametros);
        $rutaPlantilla = $this->validarVista('plantillas'.DS.$this->plantilla, true);
        $this->contenido = $this->obtenerHtmlDeArchivo($rutaPlantilla);
        Sistema::apl()->mRecursos->incluirRecursos($this->contenido);
        echo $this->contenido;
    }
    
    /**
     * Esta vista permite obtener el html generado por una vista
     * @param type $vista
     * @param array $parametros
     */
    public function mostrarVistaP($vista = '', $parametros = []){
        $rutaVista = $this->validarVista(lcfirst($this->ID).DS.$vista);
        $this->contenido = $this->obtenerHtmlDeArchivo($rutaVista, $parametros);        
        return $this->contenido;
    }
    
    /**
     * Esta función permite setear o imponer la ruta base de las vistas del controlador
     * @param string $ruta
     */
    public function setRutaVistas($ruta){
        $this->rutaVistas = $ruta;
    }
    
    /**
     * Esta función permite validar si existe una función en el controlador
     * preparada para ser llamada como la acción invocada
     * @param string $nombre
     * @return boolean
     */
    private function cargarAccion($nombre){
        $this->accion = new CAccion($nombre);
        if(!method_exists($this, $this->accion->getFn())){
            # Lógica a ejecutar si la acción no existe
            return false;
        }
        return true;        
    }    
    
    /**
     * Esta función se encarga de buscar y validar la existencia de la vista invocada
     * @param string $vista
     * @param boolean $plantilla Si es una plantilla o una vista lo que se quiere cargar
     * @return string
     * @throws CExAplicacion si ocurre algún error
     */
    private function validarVista($vista, $plantilla = false){
        
        #posible ruta de la vista en el tema, si el tema existe
        $rutaVistaTema = Sistema::apl()->tema !== null? 
                (Sistema::apl()->tema->getRutaBase().DS."vistas".DS."$vista.php") : "";
        $buscarEnTemas = Sistema::apl()->tema !== null && file_exists($rutaVistaTema);
        
        # Si se trata de un módulo siempre va a tener preferencia la plantilla
        # del tema seleccionado        
        if((!$this->perteneceAModulo && $buscarEnTemas) || 
                ($this->perteneceAModulo && $plantilla && $buscarEnTemas)){
            $rutaVista = Sistema::apl()->tema->getRutaBase().DS."vistas";
        } else {
            $rutaVista = $this->rutaVistas;
        }
        
        if(!file_exists($rutaVista) && !is_dir($rutaVista)){
            throw new CExAplicacion("No existe la ruta controlador");
        }else if(!file_exists($rutaVista.DS.$vista.'.php')){
            throw new CExAplicacion("No existe el archivo '$vista.php' dentro del directorio de "
                    . ($plantilla? "plantillas" : "vistas")
                    . ($this->perteneceAModulo? "<br><b>Modulo: Si</b>" : ""));
        }
        
        return $rutaVista.DS.$vista.'.php';
    }    
    
    /**
     * Esta función permite obtener el html generado por un script php
     * @param string $archivo ruta del archivo
     * @param array $parametros
     * @return string
     * @throws CExAplicacion Si no encuentra el archivo 
     */
    private function obtenerHtmlDeArchivo($archivo, $parametros = []){
        if(!file_exists($archivo)){
            throw new CExAplicacion("El archivo que se intenta leer no existe ($archivo)");
        }
        ob_start();
        foreach ($parametros AS $nombre=>$valor){ $$nombre = $valor; }
        include $archivo;
        return ob_get_clean();
    }
    
    /**
     * Esta función permite cargar y ejecutar un complemento
     * @param string $ruta
     * @param array $opciones
     */
    public function complemento($ruta, $opciones){
        $com = CComplemento::cargarComplemento($ruta);
        $com->asignarAtributos($opciones);
        $com->inicializar();
        return $com->iniciar();
    }
    
    /**
     * Esta función retorna el nombre de la acción cargada
     * @return string
     */
    public function getAccion(){
        return $this->accion->ID;
    }
    
    /**
     * Esta función permite indicar al controlador si pertenece o no a un módulo
     * @param boolean $b
     */
    public function perteneceAModulo($b){
        $this->perteneceAModulo = $b;
    }
}