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
    public function lista($modelo = null, $atributo = '', $elementos = array(), $opciones = array()) {
        $opHtml = $this->obtenerOpciones($modelo, $atributo, $opciones);
        $label = $this->obtenerEtiqueta($opHtml);
        $error = $this->obtenerError($modelo->getErrores(), $atributo);
        $lista = CBoot::select($modelo->$atributo, $elementos, $opHtml);
        return CHtml::e('div', $label.$error.$lista, ['class' => 'form-group']);
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
