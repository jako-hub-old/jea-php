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
    
    private $mRutas;
   
    private function __construct($rutaConfiguraciones){
        Sistema::importar('!sistema.manejadores.CMRutas');
        $this->mRutas = new CMRutas();
        echo $this->mRutas->crearUrl(array('site/index', 'ID'=>'5'));
        #cargar manejador de rutas
        #definir rutas base
        #asignar alias al sistema
        #cargar el id de la aplicación
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
    
    public function inicializar(){
        #cargar la configuración        
        #cargar los imports de la configuración
        #cargar el autoloader
        #inicializar los manejadores
    }
    
    public function iniciar(){
        
    }
    
}
