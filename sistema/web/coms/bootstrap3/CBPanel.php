<?php

class CBPanel extends CComplemento{
    public $_tipo = 'default';
    public $_cabecera = '';
    public $_cuerpo = '';
    public $_pie = '';
    
    public function inicializar() {
        $this->html = $this->construirPanel();
    }

    public function iniciar() {
        echo $this->html;
    }
    
    private function construirPanel(){
        $cabecera = $this->construirComponente($this->_cabecera, 'panel-heading');
        $cuerpo = $this->construirComponente($this->_cuerpo, 'panel-body');
        $pie = $this->construirComponente($this->_pie, 'panel-footer');
        $this->_opcionesHtml['class'] = "panel panel-$this->_tipo" . (isset($this->_opcionesHtml['class'])? $this->_opcionesHtml : '');
        return CHtml::e('div', $cabecera.$cuerpo.$pie, $this->_opcionesHtml);
    }
    
    private function construirComponente($componente, $clasePrimaria){
        if($componente == ''){
            return '';
        } else if(gettype($componente) == 'string'){
            $texto = $componente;
            $opciones = ['class' => "$clasePrimaria"];
        } else if(gettype($componente == 'array')){
            $texto = isset($componente['texto'])? $componente['texto'] : '';
            $opciones  = isset($componente)? $componente['opciones'] : [];
            $opciones['class'] = "$clasePrimaria " . (isset($opciones['class'])? $opciones['class'] : '');
        } else {
            return '';
        }
        $html = $componente !== ''? 
            CHtml::e('div', $texto, $opciones) : '';
        return $html;
    }
}
