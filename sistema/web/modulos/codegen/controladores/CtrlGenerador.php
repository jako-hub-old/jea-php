<?php
/**
 * Este es el controlador encargado de generar código
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 */
class CtrlGenerador extends CControlador{
    public $titulo = '';
    public $descripcion = '';          
    /**
     * Clase que ayuda a escanear la base de datos;
     * @var CEsquema 
     */
    private $esquema;
    /**
     * Esta clase es el generador
     * @var CGenerador 
     */
    private $generador;
    
    public function inicializar() {        
        $this->plantilla = 'generadores';
        $this->importarEsquema();
        $this->importarGenerador();
    }
    
    public function antesDeIniciar() {
        parent::antesDeIniciar();
        $accion = $this->accion->ID;
        # validamos si ya se inició sesión
        if($accion != 'login' && !Sistema::apl()->mSesion->existeAtributo("codegen_log")){
            $this->redireccionar('login');
        }
    }
    
    /**
     * Esta función se encarga de mostrar la vista de inicio de sesión y manejar
     * el logueo
     */
    public function accionLogin(){
        $this->plantilla = "login";
        $error = false;
        
        if(isset($this->_p['log'])){
            $usr = Sistema::apl()->modulo->usuario;
            $clv = Sistema::apl()->modulo->clave;
            if($usr == $this->_p['log']['username'] && $clv == $this->_p['log']['password']){
                Sistema::apl()->mSesion->setAtributo("codegen_log", $this->_p['log']['username']);
                $this->redireccionar('inicio');
            } else {
                $error = true;
            }
        }
        
        $this->mostrarVista("login", ['error' => $error]);
    }
    
    /**
     * Esta función se encarga de cerrar la sesión
     */
    public function accionLogout(){        
        if(Sistema::apl()->mSesion->existeAtributo("codegen_log")){
            Sistema::apl()->mSesion->borrarAtributo("codegen_log");
        }
        $this->redireccionar('login');
    }
    
    /**
     * Esta función muestra la vista de inicio del generador de código
     */
    public function accionInicio(){
        $this->plantilla = 'codeGen';
        $this->mostrarVista('inicio');
    }
    
    /**
     * Esta función muestra la vista para generar un modelo
     */
    public function accionModelo(){
        $this->titulo = 'Generador de modelos ' . CBoot::fa('database');
        $this->descripcion = 'Genera modelos a partir de las tablas creadas en la base de datos a la cual estas conectado';
        
        $tablas = $this->esquema->obtenerTablas();
        
        if(isset($this->_p['crear-modelo'])){
            $this->generarModelo();
            $this->redireccionar('modelo');
        }
        
        $this->mostrarVista('generarModelo', ['tablas' => $tablas]);
    }
    
    private function generarModelo(){
        # removemos el prefijo
        $t = str_replace($this->esquema->getPrefijo(), '', $this->_p['tabla']);
        $nombre = str_replace(' ', '', ucwords(str_replace('_', ' ', $t)));
        $msg = "";
        $sobreescribir = $this->_p['sobreescribir'] == 1;
        $error = false;
        if(!$sobreescribir && file_exists(Sistema::resolverRuta("!aplicacion.modelos"). DS . "$nombre.php")){
            $msg = "El modelo ya existe";
        } else {
            $modelo = $this->generador->generarModelo($this->_p['tabla']);
            if(!$modelo){
                $msg = "Ocurrió un error al generar el modelo";
                $error = true;
            } else {
                $msg = "Se generó correctamente el modelo";
            }
        }
        Sistema::apl()->mSesion->setNotificacion("modelo", ['error' => $error, 'msg' => $msg]);
    }
    
    /**
     * Esta función muestra la vista para generar un crud
     */
    public function accionCrud(){
        $this->titulo = 'Generador de CRUDS ' . CBoot::fa('list-alt');
        $this->descripcion = 'Genera un crud completo a partir de una tabla, esto agilizará el desarrollo de tu aplicación';                
        $plantilla = "basica";
        
        if(isset($this->_p['ajxreq']) && $this->_p['ajxreq'] == true){
            $this->validarCrud();
        }
        
        if(isset($this->_p['crear-crud'])){
            $plantilla = isset($this->_p['plantilla'])? $this->_p['plantilla'] : 'basica';
            $archivos = isset($this->_p['archivos'])? $this->_p['archivos'] : [];
            
            $crud = $this->generador->generarCrud($this->_p['tabla'], $archivos, $plantilla);
            $msg = "Ocurrió un error al generar el crud";
            $error = true;
            if($crud){
                $t = str_replace($this->esquema->getPrefijo(), '', $this->_p['tabla']);
                $nombre = str_replace(' ', '', ucwords(str_replace('_', ' ', $t)));                
                $link = CHtml::link("¿Deseas echar un vistazo?", [lcfirst($nombre).'/inicio'], ['target' => '_blank']);
                $msg = "Se generó correctamente el crud  $link";
                $error = false;
            }
            
            Sistema::apl()->mSesion->setNotificacion("crud", [
                'error' => $error,
                'msg' => $msg,
            ]);
            $this->redireccionar('crud');
        }
        
        $tablas = $this->esquema->obtenerTablas();
        $archivos = $this->generador->obtenerArchivosCrud($plantilla);
        
        $this->mostrarVista('generarCrud', ['tablas' => $tablas, 'archivos' => $archivos, 'plantilla' => $plantilla]);
    }
    
    private function validarCrud(){
        $t = str_replace($this->esquema->getPrefijo(), '', $this->_p['tabla']);
        $nombre = str_replace(' ', '', ucwords(str_replace('_', ' ', $t)));
        $modelo = file_exists(Sistema::resolverRuta("!aplicacion.modelos") . DS . "$nombre.php");
        $controlador = file_exists(Sistema::resolverRuta("!aplicacion.controladores") . DS . "Ctrl$nombre.php");
        $vistas = is_dir(Sistema::resolverRuta("!aplicacion.vistas." . lcfirst($nombre)));
        
        header("Content-type: application/json");
        echo json_encode([
            'existe' => $modelo || $controlador || $vistas
        ]);
        
        Sistema::fin();
    }
    
    /**
     * Esta función muestra la vista para generar un módulo 
     */
    public function accionModulo(){
        $this->titulo = 'Generador de módulos ' . CBoot::fa('cubes');
        $this->descripcion = 'Genera módulos que extiendan la funcionalidad de tu aplicación';
        if(isset($this->_p['crear-mod'])){
            $modulo = $this->generador->generarModulo($this->_p['nombre-mod']);
            # si no hubo errores
            if(!$modulo["error"]){
                # lógica en caso de que no se genere el módulo
                Sistema::apl()->mSesion->setNotificacion("modErr", 'Ocurrió un error');
            } else {           
                $codigo = "\t'" . lcfirst($modulo['clase']) . "' => [\n"
                        . "\t\t'ruta' => '" . $modulo['ruta'] . "',\n"
                        . "\t\t'clase' => '" . $modulo['clase'] . "',\n"
                        . "\t\t'controladorPorDefecto' => 'principal',\n"
                        . "\t],";
                Sistema::apl()->mSesion->setNotificacion("modCreado", [
                    'nombre' => lcfirst($modulo['clase']),
                    'html' => CHtml::e("pre", $codigo),
                ]);
                $this->redireccionar('modulo');
            }
            
            exit();
        }
        $this->mostrarVista('generarModulo', []);
    }

    /**
     * Esta función se encarga de importar la clase que permite describir las tablas
     * @throws CExAplicacion
     */
    private function importarEsquema(){
        $importacion = Sistema::importar('!web.modulos.codegen.clases.CEsquema');
        if(!$importacion){
            throw new CExAplicacion("No se pudo localizar la clase CEsquema");
        }
        $this->esquema = new CEsquema();
    }
    
    /**
     * Esta función importa el generador de código
     * @throws CExAplicacion
     */
    private function importarGenerador(){
        $importacion = Sistema::importar('!web.modulos.codegen.clases.CGenerador');
        if(!$importacion){
            throw new CExAplicacion("No se pudo localizar la clase CGenerador");
        }
        $this->generador = new CGenerador($this->esquema);
    }
    
    
}
