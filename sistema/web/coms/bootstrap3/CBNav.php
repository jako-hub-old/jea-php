<?php
/**
 * Esta clase permite generar la navbar de bootstrap
 * @package sistema.web.coms.bootstrap3
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop 
 */
class CBNav extends CComplemento{
    /**
     * Id de la barra de menú
     * @var string 
     */
    public $_id = 'bs-example-navbar-collapse';
    /**
     * Elementos del memú principal
     * @var array 
     */
    public $_elementos = [];
    /**
     * Marca de la barra de menú
     * @var string 
     */
    public $_brand;
    /**
     * Ruta a la que enviaría la marca de menú principal
     * @var string 
     */
    public $_brandUrl;
    /**
     * Tipo de navbar de bootstrap
     * <ul>
     *  <li>inverse</li>
     *  <li>defaul</li>
     * </ul>
     * @var string 
     */
    public $_tipo = 'default';
    /**
     * si la navbar es fixed o no
     * <ul>
     *  <li>top</li>
     *  <li>bottom</li>
     * </ul>
     * @var string 
     */
    public $_fixed = '';
    /**
     * Formulario del menú
     * @var array 
     */
    public $_form = null;
    /**
     * Menú de la derecha del menú
     * @var array 
     */
    public $_menuDerecha = null;        
    
    private $ruta;
    
    private $padreEncontrado = false;
    private $activo = false;
    private $posicionActiva = -1;
    
    public function __construct() {
        $this->_brand = 'Brand';
        $this->_brandUrl = Sistema::apl()->urlBase;
        $this->ruta = Sistema::apl()->ruta;
    }
    
    
    public function inicializar() {
        $this->html = $this->construirMenu();
    }

    public function iniciar() {
        echo $this->html;
    }
    
    /**
     * Esta función construye todo el menú
     * @return string
     */
    private function construirMenu(){
        $cabecera = $this->construirCabecera();
        $cuerpo = $this->construirCollapse();
        $containerFluid = CHtml::e('div', $cabecera.$cuerpo, ['class' => 'container-fluid']);
        $clase = "navbar navbar-$this->_tipo" . ($this->_fixed != ''? " navbar-fixed-$this->_fixed" : "");
        return CHtml::e('div', $containerFluid, ['class' => $clase]);
    }
    
    /**
     * Esta función construye las opciones y sub opciones de cualquier menú (izquerda o derecha)
     * Solo funciona a dos niveles, mas niveles serán ignorados
     * @param array $elementos
     * @param array $opciones
     * @param boolean $subElementos bandera para indicar si se construirán subelementos
     * @return string
     */
    private function construirOpciones($elementos = [], $opciones = [], $subElementos = false){
        $items = [];
        foreach($elementos AS $elemento){
            $texto = isset($elemento['texto'])? $elemento['texto'] : '';            
            # Si hay elementos y no se trata de crear subelementos
            if(isset($elemento['elementos']) && !$subElementos){
                $texto .= CHtml::e('span', '', ['class' => 'caret']);
                $link = CHtml::link($texto, '#', ['class' => 'dropdown-toggle','data-toggle' => 'dropdown','role' => 'button','aria-haspopup' => 'true','aria-expanded' => 'false',]);
                $subItems = $this->construirOpciones($elemento['elementos'], [$opciones], true);
                
                # si esta variable cambia a true, significa que uno de los elementos hijo está seleccionado
                $opcionesLi = [];                
                if($this->activo){
                    $opcionesLi['class'] = 'active'; $this->activo = false;
                }
                $items[] = CHtml::e('li', $link.$subItems, $opcionesLi);
            } else {
                $opcionesLi = [];
                $esActivo = isset($elemento['url']) && 
                            is_array($elemento['url']) && 
                            $elemento['url'][0] == $this->ruta;
                # activamos la opción si fue seleccionada
                if(!$this->activo && $esActivo){
                    $opcionesLi['class'] = 'active';
                    $this->activo = true;
                }
                $link = CHtml::link($texto, (isset($elemento['url'])? $elemento['url'] : '#'));
                $items[] = CHtml::e('li', $link, $opcionesLi);
            }
        }
        if(!$subElementos){            
            $opciones['class'] = "nav navbar-nav ".(isset($opciones['class'])? $opciones['class'] : '');
            return CHtml::e('ul', implode('', $items), $opciones);
        } else {
            return CHtml::e('ul', implode('', $items), ['class' => 'dropdown-menu']);
        }
    }
    
    /**
     * Esta función construye la parte collapsable del menú
     * @return string
     */
    private function construirCollapse(){
        $opciones = $this->construirOpciones($this->_elementos);
        $form = $this->construirForm();
        $menuDerecha = $this->construirMenuDerecha();
        
        return CHtml::e('div', 
                $opciones.$form.$menuDerecha,
                ['id' => $this->_id, 'class' => 'collapse navbar-collapse']
            );
    }
    
    /**
     * Esta función construye el menu de la derecha
     * @return string
     */
    private function construirMenuDerecha(){
        if($this->_menuDerecha !== null && is_array($this->_menuDerecha)){
            return $this->construirOpciones($this->_menuDerecha, ['class' => 'navbar-right']);
        } else {
            return '';
        }
    }
    
    /**
     * Esta función construye el formulario del menu (si se elige alguno)
     * @return string
     */
    private function construirForm(){
        if($this->_form !== null && is_array($this->_form)){
            $campos = isset($this->_form['campos'])? $this->_form['campos'] : [];
            $submit = isset($this->_form['submit'])? $this->_form['submit'] : '';
            $opciones = isset($this->_form['opciones'])? $this->_form['opciones'] : [];
            $ubicacion = isset($this->_form['ubicacion'])? $this->_form['ubicacion'] : 'left';
            
            $opciones['class'] = "navbar-form navbar-$ubicacion" . 
                    (isset($opciones['class'])? $opciones['class'] : "");
                 
            return CHtml::e('form', implode('', $campos).$submit, $opciones);
        } else {
            return '';
        }
    }
    
    /**
     * Esta función construye la cabecera del menú
     * @return string
     */
    private function construirCabecera(){
        $spans = str_repeat(CHtml::e('span', '', ['class' => 'icon-bar']), 3);
        $button = CHtml::boton($spans, [
            'type' => 'button', 
            'class' => 'navbar-toggle collapsed',
            'data-toggle' => 'collapse',
            'data-target' => "#$this->_id",
            'aria-expanded' => 'false',
        ]);
        $brand = CHtml::e('a', $this->_brand, ['class' => 'navbar-brand', 'href' => $this->_brandUrl]);
        return CHtml::e('div', $button.$brand, ['class' => 'navbar-header']);
    }
}