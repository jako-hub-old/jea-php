<?php echo "<?php\n"; ?>
/**
 * Esta clase representa al módulo, pueden inicializarse aquí valores 
 * valores para el funcionamiento del módulo y sus controladores
 */
class <?php echo $nClase; ?> extends CModulo{
    public function antesDeIniciar() {
        parent::antesDeIniciar();
        # definimos una plantilla por defecto para todos los controladores de este módulo
        $this->controlador->plantilla = 'intModulo';
        # aquí se puede agregar lógica para antes del inicio del módulo
    }
}
