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
        $this->plantilla = 'interiores';
        $this->importarEsquema();
        $this->importarGenerador();
    }
    
    public function accionInicio(){
        $this->plantilla = 'codeGen';
        $this->mostrarVista('inicio');
    }
    
    public function accionModelo(){
        $this->titulo = 'Generador de modelos ' . CBoot::fa('database');
        $this->descripcion = 'Genera modelos a partir de las tablas creadas en la base de datos a la cual estas conectado';
        
        $tablas = $this->esquema->obtenerTablas();
        
        if(isset($this->_p['crear-modelo'])){
            $modelo = $this->generador->generarModelo($this->_p['tabla']);
            if(!$modelo){
                # logica para el error del modelo generado
            }
        }
        
        $this->mostrarVista('generarModelo', ['tablas' => $tablas]);
    }
    
    public function accionCrud(){
        $this->titulo = 'Generador de CRUDS ' . CBoot::fa('list-alt');
        $this->descripcion = 'Genera un crud completo a partir de una tabla, esto agilizará el desarrollo de tu aplicación';
        
        
        $plantilla = "basica";
        
        if(isset($this->_p['crear-crud'])){
            $plantilla = isset($this->_p['plantilla'])? $this->_p['plantilla'] : 'basica';
            $archivos = isset($this->_p['archivos'])? $this->_p['archivos'] : [];
            
            $crud = $this->generador->generarCrud($this->_p['tabla'], $archivos, $plantilla);
            if(!$crud){
                # logica para el crud no generado
                echo "Ocurrió un error!";
                Sistema::fin();
            } else {
                $this->redireccionar('crud');
            }
        }
        
        $tablas = $this->esquema->obtenerTablas();
        $archivos = $this->generador->obtenerArchivosCrud($plantilla);
        
        $this->mostrarVista('generarCrud', ['tablas' => $tablas, 'archivos' => $archivos, 'plantilla' => $plantilla]);
    }
    
    public function accionModulo(){
        $this->titulo = 'Generador de módulos ' . CBoot::fa('cubes');
        $this->descripcion = 'Genera módulos que extiendan la funcionalidad de tu aplicación';
        if(isset($this->_p['crear-mod'])){
            $modulo = $this->generador->generarModulo($this->_p['nombre-mod']);
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

    private function importarEsquema(){
        $importacion = Sistema::importar('!web.modulos.codegen.clases.CEsquema');
        if(!$importacion){
            throw new CExAplicacion("No se pudo localizar la clase CEsquema");
        }
        $this->esquema = new CEsquema();
    }
    
    private function importarGenerador(){
        $importacion = Sistema::importar('!web.modulos.codegen.clases.CGenerador');
        if(!$importacion){
            throw new CExAplicacion("No se pudo localizar la clase CGenerador");
        }
        $this->generador = new CGenerador($this->esquema);
    }
    
    
}
