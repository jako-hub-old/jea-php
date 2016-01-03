<?php
/**
 * Esta clase es la representación de un tema de la aplicación
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna
 * @version 1.0.0
 * @copyright (c) 2016, jakop
 */
class CTema extends CComponenteAplicacion{
    private $urlBase;
    private $rutaBase;
    
    public function __construct($ID) {
        $this->ID = $ID;
    }
    
    /**
     * Esta función inicializa el componente
     */
    public function iniciar() {
        $this->rutaBase = Sistema::resolverRuta("!publico.temas.$this->ID");
        $this->urlBase = Sistema::apl()->urlBase."publico/temas/$this->ID";
    }
    
    /**
     * Esta función retorna la url base del tema
     * @return string
     */
    public function getUrlBase(){
        return $this->urlBase;
    }
    
    /**
     * Esta función retorna la ruta base del tema
     * @return string
     */
    public function getRutaBase(){
        return $this->rutaBase;
    }

}
