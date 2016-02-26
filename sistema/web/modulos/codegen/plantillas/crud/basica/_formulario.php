<?php 
$columnas = array_keys($modelo->etiquetasAtributos());
$pk = $modelo->getPk();
?>
<?php echo "<?php \n"; ?>
$formulario = new CBForm(['id' => 'form-<?php echo strtolower($nTabla); ?>']);
$formulario->abrir();
?>
<?php 
$autoFocus = ", 'autofocus' => true";
foreach ($columnas AS $col){
    if($col == $pk){
        continue;
    }
    echo "<?php echo \$formulario->campoTexto(\$modelo, '$col', ['label' => true, 'group' => true$autoFocus]) ?>\n";
    $autoFocus = "";
}
?>

<div class="row">
    <div class="col-sm-offset-6 col-sm-3">
        <?php echo "<?php echo CHtml::link(CBoot::fa('undo').' Cancelar', ['" . lcfirst($nTabla) . "/inicio'], ['class' => 'btn btn-primary btn-block']); ?>\n"; ?>
    </div>
    <div class="col-sm-3">
        <?php echo "<?php echo CBoot::boton(CBoot::fa('save') .' '. (\$modelo->nuevo? 'Guardar' : 'Actualizar'), 'success', ['class' => 'btn-block']); ?>\n"; ?>
    </div>
</div>

<?php echo "<?php \$formulario->cerrar(); ?>"; ?>