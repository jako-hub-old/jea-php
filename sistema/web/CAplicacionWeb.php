<?php
/**
 * Esta clase representa la aplicación que corre en el sistema
 * 
 * @package sistema.web
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
final class CAplicacionWeb {
    private $ID;
    private $nombre;
    private $charset = 'utf-8';    
    private $rutaConf;
    private $configuraciones = [];
    private $rutaPlantillas;
    private $tema;
    private $ruta;
    private $urlBase;
    private $rutaBase;
    private $controlador;
    private $modulo;
    
    /***************************************************************
     *  Manejadores                                                *
     ***************************************************************
     * Los manejadores son clases encargadas de funcionalidades    *
     * muy especificas                                             *
     ***************************************************************/
    private $mRecursos;
    private $mRutas;
    private $mError;
    private $mExcepcion;
    private $mRegistro;
    private $mSesion;
    
   
    private function __construct($rutaConfiguraciones){
        $this->rutaConf = $rutaConfiguraciones;
        $this->cargarConfiguracion();
        
        Sistema::importar('!sistema.manejadores.CMRutas');
        $this->mRutas = new CMRutas();
        $this->urlBase = $this->mRutas->getUrlBase();
        $this->rutaBase = $this->mRutas->getRutaBase();
        Sistema::setAlias(array(
            '!aplicacion' => $this->rutaBase . DS . 'protegido',
            '!modulos' => $this->rutaBase . DS . 'protegido' . DS . 'modulos',
            '!componentes' => $this->urlBase . DS .'protegido' . DS . 'componentes',
            '!raiz' => $this->urlBase,
        ));
    }
    
    /**
     * Este método es la única manera de obtener una instancia de la aplicación 
     * Ya que solo debería haber una aplicación circulando por todo el sistema
     * @param string $rutaConfiguraciones Ruta del archivo de configuraciones
     * @return \CAplicacionWeb
     */
    public static function getInstancia($rutaConfiguraciones){
        $instanciaAplicacion = null;
        if($instanciaAplicacion === null){
            $instanciaAplicacion = new CAplicacionWeb($rutaConfiguraciones);
        }
        return $instanciaAplicacion;
    }
    
    /**
     * Esta función inicializa los parámetros necesarios para que funcione la 
     * aplicación
     */
    public function inicializar(){
        echo "inicializando...<br>";
        if(isset($this->configuraciones['importar'])){
            $this->prepararImportaciones($this->configuraciones['importar']);
        }
        Sistema::importar('!sistema.utilidades.Autocarga');
        #inicializar los manejadores
        $this->ID = hash('md5', $this->nombre);
    }
    
    /**
     * Esta función inica la aplicación
     */
    public function iniciar(){
        echo "<br>iniciando...";
    }
    
    /**
     * Esta función carga el archivo de configuraciones
     * @throws Exception si no se encuentra creado el archivo de configuraciones
     * en la aplicación
     */
    private function cargarConfiguracion(){
        if(!file_exists(realpath($this->rutaConf))){
            throw new Exception("No se encuentra el archivo de configuración");
        }
        
        $this->configuraciones = include realpath($this->rutaConf);
        if(count($this->configuraciones) == 0){
            throw new Exception("No hay configuraciones definidas para la aplicación");
        }
        $this->setearAtributos($this->configuraciones);
    }
    
    /**
     * Esta función se encarga de setear cada uno de los atributos de la
     * aplicación que estén definidos en el archivo de configuraciones (posición: aplicacion)
     * @param array $atributos
     */
    private function setearAtributos($atributos = []){
        foreach($atributos AS $nombre=>$valor){
            if(property_exists($this, $nombre)){
                $this->$nombre = $valor;
            }
        }
    }
    
    /**
     * Esta función prepara la constante con los imports extra definidos 
     * en la configuración de la aplicación
     * @param array $aImportar
     */
    private function prepararImportaciones($aImportar = []){
        if(count($aImportar) > 0){
            define('__IMPORTACIONES__', implode(';', $aImportar));
        }
    }
    
}
