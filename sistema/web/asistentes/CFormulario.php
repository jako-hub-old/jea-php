<?php
/**
 * Esta clase esta diseñada para generar formularios a partir de un modelo
 * @package sistema.web.asistentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop
 */
class CFormulario {
    /**
     * Id para el formulario
     * @var string 
     */
    protected $id;
    /**
     * método de envio del formulario
     * @var string 
     */
    protected $metodo;
    /**
     * url a la cual el formulario enviará
     * @var string
     */
    protected $accion;
    /*
     * Opciones html para el formulario
     */
    protected $opcionesHtml;
    /**
     * Bandera para saber si el formulario fue cerrado
     * @var boolean 
     */
    protected $formularioCerrado = false;
    /**
     * Bandera para saber si aún se está trabajando en bufer, en otras palabras
     * si se intenta cerrar un formulario que no se ha abierto esta bandera ayuda a controlarlo
     * @var boolean 
     */
    private $trabajoEnBuffer = false;
    
    public function __construct($p = []) {
        $this->opcionesHtml = isset($p['opcionesHtml'])? $p['opcionesHtml'] : [];
        $this->id = isset($p['id'])? $p['id'] : '';
        $this->metodo = isset($p['metodo'])? $p['metodo'] : 'POST';
        $this->accion = isset($p['accion'])? $p['accion'] : '';
        $this->inicializar();
    }
    
    
    /**
     * El formulario debe ser cerrado antes de que inicie la aplicación
     * @throws CExAplicacion
     */
    public function __destruct() {
        if(!$this->formularioCerrado){
            # he comentado esto por que estaré discutiendo si conservarlo o no
//            throw new CExAplicacion("No se ha cerrado apropiadamente el formualrio, recuerde usar \$formulario->cerrar())");
        }
    }
    
    /**
     * Esta función inicializa los valores basicos del formulario
     */
    private function inicializar(){
        $this->opcionesHtml['id'] = $this->id;
        $this->opcionesHtml['method'] = $this->metodo;
        $this->opcionesHtml['action'] = $this->accion;
    }
    
    /**
     * Todo lo que suceda despues de llamar esta función será capturado como 
     * el contenido del formulario
     */
    public function abrir(){
        $this->trabajoEnBuffer = true;
        ob_start();        
    }
    
    /**
     * Esta función captura todo el contenido desde la apertura del formulario y 
     * lo imprime
     * 
     * @throws CExAplicacion Si no se ha abierto el formulario
     */
    public function cerrar(){
        if(!$this->trabajoEnBuffer){
            throw new CExAplicacion("Para poder cerrar el formulario primero debes abrirlo \$formulario->abrir()");
        }
        $this->trabajoEnBuffer = false;
        $this->formularioCerrado = true;
        $contenido = ob_get_clean();
        echo CHtml::e("form", $contenido, $this->opcionesHtml);
    }
    
    /**
     * 
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $opciones
     * @return string
     */
    public function campoTexto($modelo = null, $atributo = '', $opciones = []){
        $opcHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $valor = $modelo->$atributo;
        return CHtml::campoTexto($valor, $opcHtml);
    }
    
    /**
     * Esta función crea un area de texto con los atributos del modelo
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $opciones
     * @return string
     */
    public function areaTexto($modelo = null, $atributo = '', $opciones = []){
        $opcHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $valor = $modelo->$atributo;
        return CHtml::areaTexto($valor, $opcHtml);
    }
    
    /**
     * Esta función crea un campo lista de selección 
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $elementos
     * @param array $opciones
     * @return string
     */
    public function lista($modelo = null, $atributo = '', $elementos = [], $opciones = []){
        $opcHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $valor  = $modelo->$atributo;
        return CHtml::lista($valor, $elementos, $opcHtml);
    }
        
    public function campoArchivo($modelo = null, $atributo = '', $opciones = []){
        $opcHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $valor = $modelo->$atributo;
        return CHtml::input('file', $valor, $opcHtml);
    }
    
    public function campoPassword($modelo = null, $atributo = '', $opciones = []){
        $opcHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $valor = $modelo->$atributo;
        return CHtml::input('password', $valor, $opcHtml);
    }
    
    /**
     * 
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $opciones 
     * @throws CExAplicacion
     */
    protected function obtenerOpciones($modelo, $atributo, $opciones = []){
        if($modelo === null){
            throw new CExAplicacion("El modelo no puede estar vacio");
        }
        if($atributo === null || trim($atributo) === ''){
            throw new CExAplicacion("El atributo no puede estar vacio");
        }
        # obtenemos el nombre de la tabla
        $nModelo = str_replace(' ', '', ucwords(str_replace('_', ' ', $modelo->tabla())));
        $opcBasicas = [
            'name' => ucfirst($nModelo)."[$atributo]",
            'id' => $nModelo."_".$atributo,
        ];
        
        # buscamos si hay label
        if(isset($opciones['label']) && $opciones['label'] == true){
            $opciones['label'] = $modelo->obtenerEtiqueta($atributo);
        }
        
        # lo que retornamos es una mezcla entre las opciones ingresadas y las básicas
        # si hay posiciones con el mismo nombre en las opciones ingresadas, estas
        # sobreescribirán el valor de las opciones básicas
        return array_merge($opcBasicas, $opciones);
    }
    
    public function obtenerEtiqueta(&$opHtml = []){
        $label = '';
        if(isset($opHtml['label'])){
            $label = CHtml::e('label', $opHtml['label'], ['for' => $opHtml['id']]);
            unset($opHtml['label']);
        }
        return $label;
    }
    
    public function mostrarErrores($modelo){
        # pendiente por implementar
        return $modelo;
    }
}