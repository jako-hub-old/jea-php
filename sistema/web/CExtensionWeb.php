<?php

abstract class CExtensionWeb {
    
    public function setAtributos($atributos = []){
        foreach ($atributos AS $atr => $valor){
            if(property_exists($this, $atr)){
                $this->$atr = $valor;
            }
        }
    }
    
}
