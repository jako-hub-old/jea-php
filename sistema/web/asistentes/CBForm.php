<?php
/**
 * Esta clase es la versión de CFormulario con estilos de bootstrap
 * @package sistema.web.asistentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop 
 */
class CBForm extends CFormulario{
    /**
     * Esta función permite generar un campo de texto con estilos de bootstrap
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $opciones
     * @return string
     */
    public function campoTexto($modelo = null, $atributo = '', $opciones = array()) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->ObtenerError($modelo->getErrores(), $atributo);
        $input = CBoot::text($modelo->$atributo, $opHtml);
        return CHtml::e('div', $label.$error.$input, ['class' => 'form-group']);
    }
    
    public function inputAddon($modelo = null, $atributo = '', $tipo = 'texto', $opciones = array(), $addOn = '', $pre = false){
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $icon = CBoot::fa($addOn);
        if($pre) {
            $opHtml['pre'] = $icon;
        } else {
            $opHtml['pos'] = $icon;
        }
        $input = CBoot::fieldAddOn($modelo->$atributo, $tipo, $opHtml);
        return CHtml::e('div', $label.$error.$input, ['class' => 'form-group']);
    }
    
    public function campoArchivo($modelo = null, $atributo = '', $opciones = array()) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $input = CBoot::fileInput($modelo->$atributo, $opHtml);
        return CHtml::e('div', $label.$error.$input, ['class' => 'form-group']);
    }
    
    public function campoPassword($modelo = null, $atributo = '', $opciones = array()) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $input = CBoot::passwordField($modelo->$atributo, $opHtml);
        return CHtml::e('div', $label.$error.$input, ['class' => 'form-group']);
    }
    
    /**
     * Esta función permite generar un area de texto con estilos de bootstrap
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $opciones
     * @return string
     */
    public function areaTexto($modelo = null, $atributo = '', $opciones = array()) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $text = CBoot::textArea($modelo->$atributo, $opHtml); 
        return CHtml::e('div', $label . $error . $text, ['class' => 'form-group']);
    }
    /**
     * Esta función permite generar una lista de selección con estilos de bootstrap
     * @param CModelo $modelo
     * @param string $atributo
     * @param array $elementos
     * @param array $opciones
     * @return string
     */
    public function lista($modelo = null, $atributo = '', $elementos = [], $opciones = []) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $lista = CBoot::select($modelo->$atributo, $elementos, $opHtml);
        return CHtml::e('div', $label.$error.$lista, ['class' => 'form-group']);
    }
    
    public function radioButtons($modelo = null, $atributo = '', $elementos = [], $opciones = []){
        $opG = $this->obtenerOpciones($modelo, $atributo, $opciones); #opciones para todos
        $label = $this->obtenerEtiqueta($opG);
        
        if($label != ""){ $label = CHtml::e('p', $label); }
        
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        
        $inputs = $this->construirRadioInputs($modelo, $atributo, $elementos, $opG);
        $opciones['class'] = 'btn-group' . (isset($opciones['class'])? $opciones['class'] : '');
        $opciones['data-toggle'] = 'buttons';
        
        unset($opciones['label']);
        $group = CHtml::e('div', $error . implode('', $inputs), $opciones);
        
        return CHtml::e('div', $label . $group, ['class' => 'form-group']);
    }
    
    private function construirRadioInputs($modelo, $atributo, $elementos, $opciones){
        $inputs = [];
        foreach($elementos AS $clave=>$e){
            $tipo = 'btn btn-'.(isset($e['tipo'])? $e['tipo'] : 'primary');
            $texto = isset($e['texto'])? $e['texto'] : "Opcion " . ($clave + 1);
            $valor = isset($e['valor'])? $e['valor'] : $clave;
            $opE = $opciones;
            $opE['id'] = $opE['id'] . "_$clave";
            $opE['autocomplete'] = 'off';
            $activo = $modelo->$atributo !== "" && $modelo->$atributo == $valor;
            if($activo){
                $opE['checked'] = true;
            }
            $input = CHtml::input('radio', $valor, $opE). " $texto";
            $inputs[] = CHtml::e('label', $input, ['class' => "$tipo". ($activo? ' active' : '') ]);
        }
        return $inputs;
    }
    
    private function obtenerError($log = [], $campo = ''){  
        $r = isset($log['requeridos'])? $log['requeridos'] : false;
        if($r === false || array_search($campo, $r) === false){
            return '';
        }        
        return CHtml::e('p', "El campo <b>$campo</b> no puede estar vacio", ['class' => 'text-danger requerido']);
    }
    
    /**
     * 
     * @param CModelo $modelo
     */
    public function mostrarErrores($modelo, $opciones = []) {
        if(!$modelo->hayError()){
            return null;
        }
        # preguntamos primero por los campos requeridos
        $html = $this->camposRequeridos($modelo->getErrores());
        return CBoot::alert($html, 'danger', $opciones);
    }
    
    private function camposRequeridos($log){
        $requeridos = isset($log['requeridos'])? $log['requeridos'] : false;
        if($requeridos === false){
            return '';
        }
        $html = CHtml::e('p', "Los siguientes campos son requeridos:  ");
        $li = implode('', array_map(function($v){ return CHtml::e('li', $v); }, $requeridos));
        $html .= CHtml::e('ul', $li);
        
        return $html;
    }
}
