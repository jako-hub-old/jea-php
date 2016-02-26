<?php echo "<?php\n"?>
/**
 * Este es el controlador <?php echo $nTabla; ?>, desde aquí se gestionan
 * todas las actividades que tengan que ver con <?php echo $nTabla."\n"; ?>
 * @author <?php echo $autor."\n"; ?>
 * @version 1.0.0
 */
class Ctrl<?php echo $nTabla; ?> extends CControlador{
    
    /**
     * Esta función muestra el inicio y una tabla para listar los datos
     */
    public function accionInicio(){
        $modelos = <?php echo $nTabla; ?>::modelo()->listar();        
        $this->mostrarVista('inicio', ['modelos' => $modelos]);
    }
    
    /**
     * Esta función permite crear un nuevo registro
     */
    public function accionCrear(){
        $modelo = new <?php echo $nTabla; ?>();
        if(isset($this->_p['<?php echo $nTabla; ?>'])){
            $modelo->atributos = $this->_p['<?php echo $nTabla; ?>'];
            if($modelo->guardar()){
                # lógica para guardado exitoso
                $this->redireccionar('inicio');
            }
        }
        $this->mostrarVista('crear', ['modelo' => $modelo]);
    }
    
    /**
     * Esta función permite editar un registro existente
     * @param int $pk
     */
    public function accionEditar($pk){
        $modelo = $this->cargarModelo($pk);
        if(isset($this->_p['<?php echo $nTabla; ?>'])){
            $modelo->atributos = $this->_p['<?php echo $nTabla; ?>'];
            if($modelo->guardar()){
                # lógica para guardado exitoso
                $this->redireccionar('inicio');
            }
        }
        $this->mostrarVista('editar', ['modelo' => $modelo]);
    }
    
    /**
     * Esta función permite ver detalladamente un registro existente
     * @param int $pk
     */
    public function accionVer($pk){
        $modelo = $this->cargarModelo($pk);
        $this->mostrarVista('ver', ['modelo' => $modelo]);
    }
    
    /**
     * Esta función permite eliminar un registro existente
     * @param int $pk
     */
    public function accionEliminar($pk){
        $modelo = $this->cargarModelo($pk);
        if($modelo->eliminar()){
            # lógica para borrado exitoso
        } else {
            # lógica para error al borrar
        }
        $this->redireccionar('inicio');
    }
    
    /**
     * Esta función permite cargar un modelo usando su primary key
     * @param int $pk
     * @return <?php echo $nTabla . "\n"; ?>
     */
    private function cargarModelo($pk){
        return <?php echo $nTabla; ?>::modelo()->porPk($pk);
    }
}