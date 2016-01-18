<?php
/**
 * Este es el módulo generador de código
 */
class CodeGen extends CModulo{
   protected $_usuario = '';
   protected $_clave = '';
   
   public function getUsuario(){
       return $this->_usuario;       
   }
   
   public function getClave(){
       return $this->_clave;
   }
   
}
