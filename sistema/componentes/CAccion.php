<?php
/**
 * Esta clase se usa para instanciar acciones de los controladores
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, 2015
 */

final class CAccion extends CComponenteAplicacion{
    public function __construct($ID) {
        $this->ID = $ID;
    }
    
    public function getFn(){
        return 'accion' .  ucfirst($this->ID);
    }
    
    public function init() {}
    public function iniciar() {
        
    }
}
