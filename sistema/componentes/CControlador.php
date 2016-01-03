<?php
/**
 * Esta clase será la plantilla de todos los controladores de la aplicación
 * contiene los elementos necesarios para que un controlador cumpla con su función
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2015, 2015
 */
abstract class CControlador extends CComponenteAplicacion{
    
    /**
     * Instancia de la ación llamada para el controlador
     * @var CAccion 
     */
    protected $accion;
    protected $contenido;
    protected $plantilla = 'basica';
    protected $tituloPagina;
    protected $rutaVistas;
    private $nombreAccion;


    public function __construct($ID =__CLASS__, $accion) {
        $this->ID = $ID;
        $this->nombreAccion = $accion;
        $this->rutaVistas = Sistema::resolverRuta('!aplicacion.vistas');
        $this->tituloPagina = $ID . ' - ' . $accion;
    }
    
    /**
     * Se puede usar para inicializar valores del controlador, o escribir lógica
     * para que se ejecute antes de llamar cualquier acción
     */
    public function inicializar(){}    
    
    /**
     * Esta función inicia el controlador
     */
    public function iniciar() {
        $parametros = filter_input_array(INPUT_GET);
        $accion = $this->cargarAccion($this->nombreAccion);
        
        if(!$accion){ throw new CExAplicacion("La acción solicitada no está creada ($this->nombreAccion)"); }
        
        unset($parametros['r']);
        
        // validamos si hay parámetros para agregar a la función
        // NOTA: solo se agregará un parámetro a la función
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
        if(count($partes) === 1){
            $ruta = $this->ID.'/'.$ruta;
        }
        $url = Sistema::apl()->crearUrl(array_merge(array($ruta), $parametros));
        header("location:".$url);
    }
    
    /**
     * Esta función permite mostrar una vista
     * @param string $vista
     * @param array $parametros
     */
    public function mostrarVista($vista = '', $parametros = []){
        $rutaVista = $this->validarVista(lcfirst($this->ID).DS.$vista);
        $this->contenido = $this->obtenerHtmlDeArchivo($rutaVista, $parametros);        
        $rutaPlantilla = $this->validarVista('plantillas'.DS.$this->plantilla);
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
     * @return string
     * @throws CExAplicacion si ocurre algún error
     */
    private function validarVista($vista){
        // preguntamos si el tema fue definido, si es así tratamos de armar 
        // la nueva ruta de las vistas desde la ubicación del tema
        if(Sistema::apl()->tema !== null &&
            file_exists(Sistema::apl()->tema->getRutaBase().DS."vistas".DS."$vista.php")){
            $rutaVista = Sistema::apl()->tema->getRutaBase().DS."vistas";
        }else{
            $rutaVista = $this->rutaVistas;
        }
        
        if(!file_exists($rutaVista) && !is_dir($rutaVista)){
            throw new CExAplicacion("No existe la ruta controlador");
        }else if(!file_exists($rutaVista.DS.$vista.'.php')){
            throw new CExAplicacion("No existe el archivo '$vista'.php dentro del directorio de vistas");
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
}
