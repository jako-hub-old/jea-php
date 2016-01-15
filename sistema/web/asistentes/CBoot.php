<?php
/**
 * Esta clase es el asistente para generar html con clases de bootstrap
 * @package sistema.web.asistentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2014, jakop
 */
final class CBoot {
    
    const BTN = 'default';
    const BTN_P = 'primary';
    const BTN_S = 'success';
    const BTN_W = 'warning';
    const BTN_D = 'danger';
    const BTN_I = 'info';
    
    const INPUTS_CLASS = 'form-control';
    
    private function __construct() {}
    
    ### botones ###
    
    /**
     * Esta función retorna el html de un botón con estilos de boostrap
     * @param string $nombre texto que saldrá en el botón
     * @param string $tipo
     * @param array $opciones
     * @return string
     */
    public static function boton($nombre = '', $tipo = 'default', $opciones = []) {
        $clase = "btn btn-$tipo";
        $opciones['class'] = isset($opciones['class'])? "$clase " . $opciones['class'] : $clase;
        if(isset($opciones['group']) && $opciones['group'] == true){
            unset($opciones['group']);
            return CHtml::e('div',CHtml::boton($nombre, $opciones), ['class' => 'form-group']);
        }
        return CHtml::boton($nombre, $opciones);
    }
    
    /**
     * Esta función devuelve el html de un botón primary
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonP($nombre, $opciones = []){
        return self::boton($nombre, self::BTN_P, $opciones);
    }
    
    /**
     * Esta función devuelve el html de un botón success
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonS($nombre, $opciones = []){
        return self::boton($nombre, self::BTN_S, $opciones);
    }    
    
    /**
     * Esta función devuelve el html de un botón warning
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonW($nombre, $opciones = []){
        return self::boton($nombre, self::BTN_W, $opciones);
    }    
    
    /**
     * Esta función devuelve el html de un botón danger
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonD($nombre, $opciones = []){
        return self::boton($nombre, self::BTN_D, $opciones);
    }
    
    /**
     * Esta función devuelve el html de un botón info
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonI($nombre, $opciones = []){
        return self::boton($nombre, self::BTN_I, $opciones);
    }
    
    ### inputs
    
    /**
     * Esta función crea un input con la clase control de boostrap form-control
     * @param string $tipo
     * @param string $valor
     * @param array $opciones
     * @return string
     */
    public static function input($tipo = 'text', $valor = '', $opciones = []){
        # no queremos que se ponga el tipo en las opciones
        unset($opciones['type']);
        $opciones['class'] = isset($opciones['class'])? "form-control ".$opciones['class'] : 'form-control';
        
        if(isset($opciones['group'])){
            # construimos el label, también si hay opciones para el label se las pasamos
            $label = isset($opciones['label'])? 
                CHtml::e("label", $opciones['label'], isset($opciones['opcionesLabel'])? $opciones['opcionesLabel'] : []) :
                "";
            unset($opciones['group']);
            unset($opciones['label']);
            return CHtml::e('div', $label.CHtml::input($tipo, $valor, $opciones), ['class' => 'form-group']);
        } else{            
            return CHtml::input($tipo, $valor, $opciones);
        }
    }
    
    public static function submit($nombre, $tipo = 'default', $opciones = []){
        unset($opciones['tipo']);
        $valor = str_replace(' ', '_', $nombre);
        $opciones['class'] = "btn btn-$tipo ".(isset($opciones['class'])? $opciones['class'] : "");
        return CHtml::input('submit', $valor, $opciones);
    }
    
    /**
     * Esta función crea un campo de texto con estilos de bootstrap
     * @param string $valor
     * @param array $opciones
     * @return string
     */
    public static function text($valor = '', $opciones = []){
        return self::input('text', $valor, $opciones);
    }
    
    /**
     * Esta función permite crear los addons de bootstrap
     * @param string $valor valor del input
     * @param string $tipo success, warning, error
     * @param array $opciones opciones html del input, se puede incluir
     *              <ul>
     *                 <li><b>pre[string]: </b>preppended</li>
     *                 <li><b>pos[string]: </b>appended</li>
     *                 <li><b>pre-btn[string]: </b>appended button</li>
     *                 <li><b>pos-btn[string]: </b>appended button</li>     
     *              </ul>
     * @return type
     */    
    private static function inputAddOn($valor = '', $tipo = 'text', $opciones = []){
        $pre = '';
        $pos = '';
        # agregamos el tokken que se prepone
        if(isset($opciones['pre']) || isset($opciones['pre-btn'])){
            $clase = isset($opciones['pre-btn']) ? 'btn' : 'addon';
            $contenido = isset($opciones['pre'])? $opciones['pre'] : $opciones['pre-btn'];
            $pre = CHtml::e('span', $contenido, ['class' => "input-group-$clase"]);
            unset($opciones['pre']);
            unset($opciones['pre-btn']);
        }
        # agregamos el tokken que se sobrepone
        if(isset($opciones['pos']) || isset($opciones['pos-btn'])){
            $clase = isset($opciones['pos-btn']) ? 'btn' : 'addon';
            $contenido = isset($opciones['pos'])? $opciones['pos'] : $opciones['pos-btn'];
            $pos = CHtml::e('span', $contenido, ['class' => "input-group-$clase"]);
            unset($opciones['pos']);
            unset($opciones['pos-btn']);
        }
        
        $group = (isset($opciones['group']) && $opciones['group'] == true);
        unset($opciones['group']);
        
        $input = self::input($tipo, $valor, $opciones);
        $html = ($pre !== '' || $pos !== '')?
            CHtml::e('div', $pre.$input.$pos, ['class' => 'input-group']) : 
            $input;
        
        return $group? 
            CHtml::e('div', $html, ['class' => 'form-group']) :
            $html;                
    }
    
    public static function textAddOn($valor = '', $opciones = []){
        return self::inputAddOn($valor, 'text', $opciones);
    }
    
    public static function passwordAddOn($valor = '', $opciones = []){
        return self::inputAddOn($valor, 'password', $opciones);
    }


    /**
     * Esta función crea un textarea con estilos de bootstrap
     * @param string $valor
     * @param array $opciones
     * @return string
     */
    public static function textArea($valor = '', $opciones = []){
        $opciones['class'] = isset($opciones['class'])? "form-control ".$opciones['class'] : 'form-control';
        if(isset($opciones['group']) && $opciones['group'] == true){
            unset($opciones['group']);
            return CHtml::e('div', CHtml::areaTexto($valor, $opciones),['class' => 'form-group']);
        }
        return CHtml::areaTexto($valor, $opciones);
    }
    
    /**
     * Esta función permite crear una lista select con los estilos de bootstrap
     * @param mixed $seleccion entero o cadena, es la opción seleccionada
     * @param array $elementos
     * @param array $opciones
     * @return string
     */
    public static function select($seleccion = '', $elementos = [], $opciones = []){
        $opciones['class'] = isset($opciones['class'])? "form-control ".$opciones['class'] : 'form-control';
        # si hay que agrupar
        if(isset($opciones['group']) && $opciones['group'] == true){
            unset($opciones['group']);
            return CHtml::e('div', CHtml::lista($seleccion, $elementos, $opciones),['class' => 'form-group']);
        }
        return CHtml::lista($seleccion, $elementos, $opciones);
    }
    
    /**
     * Esta función permite crear los inputs validados de bootstrap
     * @param string $valor valor del input
     * @param string $tipo success, warning, error
     * @param array $opciones opciones html del input, se puede incluir
     *              <ul>
     *                 <li><b>label[string]: </b>Etiqueta para el input</li>
     *                 <li><b>addon[boolean]: </b>Si se incluirá addon</li>
     *                 <li><b>pre[string]: </b>preppended</li>
     *                 <li><b>pos[string]: </b>appended</li>
     *                 <li><b>pre-btn[string]: </b>appended button</li>
     *                 <li><b>pos-btn[string]: </b>appended button</li>
     *                 <li><b>feedback[string]: </b>Si habrá feedback para el input</li>
     *              </ul>
     * @return type
     */
    public static function inputValidate($valor = '', $tipo = 'success', $opciones = []){
        # construimos el label        
        if(isset($opciones['label'])){
            $opcLabel = isset($opciones['opcionesLabel'])?
                    $opciones['opcionesLabel'] : 
                    ['class' => 'control-label'];
            $label = CHtml::e('label', $opciones['label'], $opcLabel);
        } else {
            $label = '';
        }
        # construimo el feedback (si hay)
        if(isset($opciones['feedback'])){
            $feedback = self::glyphicon($opciones['feedback'], ['class' => 'form-control-feedback', 'aria-hidden' => 'true']);
            unset($opciones['feedback']);
        } else {
            $feedback = '';
        }        
        # Construimos el input, si hay addon se incluye
        if(isset($opciones['addon']) && $opciones['addon'] === true){
            $input = self::textAddOn($valor, $opciones);
            unset($opciones['addon'], $opciones['pre'], $opciones['pos'], $opciones['pre-btn'], $opciones['pos-btn']);
        } else {
            $input = self::text($valor, $opciones);
        }
        return CHtml::e('div', $label.$input.$feedback, ['class' => "form-broup has-$tipo ". ($feedback == ""? "" : " has-feedback")]);
    }   
    
    ######################## componentes de bootstrap ###############################
    
    /**
     * Esta función permite crear la iconografía de bootstrap
     * @param string $icono nombre de la clase glyphicon, no es necesario anexar el prefijo glyphicon-
     * @param array $opciones
     * @return string
     */
    public static function glyphicon($icono = '', $opciones = []){
        $opciones['class'] = isset($opciones['class'])? 
                "glyphicon glyphicon-$icono " . $opciones['class'] : 
                "glyphicon glyphicon-$icono";
        return CHtml::e('span', '', $opciones);
    }        

    /**
     * Esta función permite crear ya se aun drop down o un dropup
     * @param string $nombre
     * @param string $tipoBtn clase para el botón 
     * @param [] $elementos elementos del menú
     * @param array $opciones
     * @param string $tipo tipo de drop, up o down
     * @return string
     */
    private static function dropList($nombre, $tipoBtn = 'default', $elementos = [], $opciones = [], $tipo = 'dropdown', $cerrar = true){
        $items = [];
        # inponemos un id
        $id = str_replace(' ', '_', $nombre);
        if($tipoBtn == '' || $tipoBtn === null){ $tipoBtn = 'default'; }
        
        # construimos los elementos del dropdown
        foreach ($elementos AS $elemento){
            $texto = isset($elemento['texto'])? $elemento['texto'] : '';            
            if(isset($elemento['header']) && $elemento['header'] == true){
                $items[] = CHtml::e('li',$texto, ['class' => 'dropdown-header']);
            } else {                
                $link = CHtml::link($texto, (isset($elemento['url'])? $elemento['url'] : '#'), []);
                $items[] = CHtml::e('li',$link, isset($elemento['opciones'])? $elemento['opciones'] : []);
            }
        }
        
        $ul = CHtml::e('ul', implode('', $items), ['class' => 'dropdown-menu', 'aria-labelleadby' => $id]);
        # construimos las opciones del botón del dropdown
        $btnOpc = ['class' => 'dropdown-toggle', 'type' => 'button', 'id' => $id,
            'data-toggle' => 'dropdown','aria-haspopup' => "true",'aria-expanded' => "true",];
        $caret = CHtml::e("span",'',['class' => 'caret']);        
        $boton = self::boton("$nombre $caret", $tipoBtn, $btnOpc);
        $opciones['class'] = isset($opciones['class'])? "$tipo " . $opciones['class'] : $tipo;
        if($cerrar){ return CHtml::e('div', $boton.$ul, $opciones); } 
        else { return $boton.$ul; }
    }
    
    /**
     * Esta función permite crear un dropdown de bootstrap
     * @param string $nombre nombre del dropdown
     * @param string $tipoBtn clase para el botón del dropdown
     * @param [] $elementos
     * @param [] $opciones
     * @return string
     */
    public static function dropDown($nombre, $tipoBtn = 'default', $elementos = [], $opciones = [], $cerrar = true){
        return self::dropList($nombre, $tipoBtn, $elementos, $opciones, 'dropdown', $cerrar);
    }
    
    /**
     * Esta función permite crear un dropup de bootstrap
     * @param string $nombre nombre del dropup
     * @param string $tipoBtn clase para el botón del dropdown
     * @param [] $elementos
     * @param [] $opciones
     * @return string
     */
    public static function dropUp($nombre, $tipoBtn = 'default', $elementos = [], $opciones = [], $cerrar = true){
        return self::dropList($nombre, $tipoBtn, $elementos, $opciones, 'dropup', $cerrar);
    }
    
    /**
     * Esta función permite crear grupos de botones
     * @param [] $elementos
     * @param [] $opciones
     * @param string $clase
     * @return string
     */
    public static function buttonGroup($elementos = [], $opciones = [], $clase = 'btn-group'){
        $items = [];
        foreach($elementos AS $elemento){
            $nombre = isset($elemento['nombre'])? $elemento['nombre'] : '';
            $tipo = isset($elemento['tipo'])? $elemento['tipo'] : 'default';
            $opcE = isset($elemento['opciones'])? $elemento['opciones'] : [];
            $opcE['class'] = isset($opcE['class'])? "btn-group ".$opcE['class'] : 'btn-group';
            $items[] = self::boton($nombre, $tipo, $opcE);
        }
        $opciones['class'] = isset($opciones['class'])? "$clase ".$opciones['class'] : $clase;
        $opciones['role'] = 'group';
        return CHtml::e("div", implode('', $items), $opciones);
    }
    
    /**
     * Esta función permite crear los labels de bootstrap
     * @param string $texto
     * @param string $tipo
     * @param array $opciones
     * @return string
     */
    public static function label($texto = '', $tipo = 'default', $opciones = []){
        $opciones['class'] = "label label-".(isset($opciones['class'])? "$tipo ".$opciones['class'] : "$tipo");
        return CHtml::e('span', $texto, $opciones);
    }
    
    /**
     * Esta función permite crear los badges de bootstrap
     * @param string $texto
     * @param string $tipo
     * @param array $opciones
     * @return string
     */
    public static function badge($texto = '', $tipo = "", $opciones = []){
        $opciones['class'] = "badge badge-".(isset($opciones['class'])? "$tipo ".$opciones['class'] : "$tipo");
        return CHtml::e('span', $texto, $opciones);
    }
    
    /**
     * Esta función genera los alerts de bootstrap
     * @param string $texto
     * @param string $tipo
     * @param array $opciones
     * @param boolean $cerrar
     * @return string
     */
    public static function alert($texto = '', $tipo = 'success', $opciones = [], $cerrar = false){
        $boton = '';
        if($cerrar){
            $opcBtn = ['class' => 'close', 'type' => 'button', 'data-dismiss' => 'alert', 'aria-label' => 'Close'];
            $boton = CHtml::boton(CHtml::e('span', '&times;', ['aria-hidden' => 'true']), $opcBtn);
        }
        $opciones['class'] = "alert alert-".(isset($opciones['class'])? "$tipo ".$opciones['class'] : "$tipo") . " alert-dismissible";
        $opciones['role'] = 'alert';
        $html = CHtml::e('div', $texto.$boton, $opciones);
        
        return $html;
    }
    
    /**
     * Esta función permite crear las progress bar de bootstrap
     * @param int $porcentaje
     * @param [] $tipo
     * @param [] $opciones
     * @return string
     */
    public static function progressBar($porcentaje = 0, $tipo = '', $opciones = []){
        $opciones['class'] = "progress-bar progress-bar-".(isset($opciones['class'])? "$tipo ".$opciones['class'] : "$tipo");
        $opciones['role'] = 'progressbar';
        $opciones['aria-valuenow'] = $porcentaje;
        $opciones['aria-valuemin'] = '0';
        $opciones['aria-valiuemax'] = '100';
        $opciones['style'] = "width: $porcentaje%";
        $interior = CHtml::e("div", ($porcentaje == ""? '' : "$porcentaje%"), $opciones);
        return CHtml::e('div', $interior, ['class' => 'progress']);
    }
    
    /**
     * Esta función es la base para crear lista
     * @param [] $elementos
     * @param array $opciones
     * @param string $c etiqueta contenedora
     * @param string $i etiqueta de elementos
     * @return string
     */
    private static function listGroupBase($elementos = [], $opciones = [], $c = 'div', $i = 'div'){
        $items = [];
        foreach ($elementos AS $elemento){
            $texto = isset($elemento['texto'])? $elemento['texto'] : '';
            $opcE = isset($elemento['opciones'])? $elemento['opciones'] : [];
            $opcE['class'] = "list-group-item ".(isset($opcE['class'])? $opcE['class'] : '');
            if(isset($opcE['url'])){
                $opcE['href'] = Sistema::apl()->crearUrl($opcE['url']);
                unset($opcE['url']);
            }
            $items[] = CHtml::e($i, $texto, $opcE);
        }
        $opciones['class'] = "list-group ".(isset($opciones['class'])? $opciones['class'] : '');
        return CHtml::e($c, implode('', $items), $opciones);
    }
    
    /**
     * Esta Función permite crear listas de elementos bootstrap con ul y li
     * @param [] $elementos
     * @param [] $opciones
     * @return string
     */
    public static function listGroup($elementos = [], $opciones = []){
        return self::listGroupBase($elementos, $opciones, 'ul', 'li');
    }
    
    /**
     * Esta función permite crear listas de elementos de bootstrap con div y a
     * @param []$elementos
     * @param [] $opciones
     * @return string
     */
    public static function linkedList($elementos = [], $opciones = []){
        return self::listGroupBase($elementos, $opciones, 'div', 'a');
    }
    
    /**
     * Esta función permite crear listas de elementos de bootstrap con div y button
     * @param [] $elementos
     * @param [] $opciones
     * @return string
     */
    public static function buttonList($elementos = [], $opciones = []){
        return self::listGroupBase($elementos, $opciones, 'div', 'button');
    }
    
    /**
     * Esta función permite crear la iconografía de font awesome
     * @param string $icono
     * @param array $opciones
     * @return string
     */
    public static function fa($icono, $opciones = []){
        $opciones['class'] = isset($opciones['class'])? 
                "fa fa-$icono " . $opciones['class'] : 
                "fa fa-$icono";
        return CHtml::e('i', '', $opciones);
        
    }
}