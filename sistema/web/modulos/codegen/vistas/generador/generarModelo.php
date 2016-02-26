<?php 
$f = new CBForm([
    'id' => 'crear_modelo',
]);
$f->abrir();
?>
<?php if(Sistema::apl()->mSesion->existeNotificacion("modelo")): ?>
<?php $not = Sistema::apl()->mSesion->getNotificacion("modelo"); ?>
<div class="alert alert-<?php echo ($not['error'] == true? 'danger' : 'success'); ?>">
    <?php echo $not['msg']; ?>
</div>
<?php endif; ?>

<div class="form-group">
    <label>¿De que tabla generará el modelo?</label>
    <?php echo CBoot::select('', $tablas, ['name' => 'tabla', 'id' => 'tabla', 'defecto' => 'Seleccione una tabla', 'data-select-two' => 'true']); ?>
</div>
<div class="form-group">
    <label>¿Sobreescribir el modelo si ya existe?</label>
    <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-primary">
            <input type="radio" name="sobreescribir" id="override-yes" autocomplete="off" value="1"> Si
        </label>
        <label class="btn btn-default active">
            <input type="radio" name="sobreescribir" id="override-no" autocomplete="off" value="0" checked> No
        </label>
    </div>
</div>
<div class="form-group">
    <?php echo CBoot::botonS(CBoot::fa('pencil-square-o') . ' Crear modelo', ['id' => 'crear', 'name' => 'crear-modelo', 'class' => 'btn-block', 'disabled' => 'disabled'])?>
</div>

<?php $f->cerrar(); ?>
<script>
    jQuery(function(){
        setTimeout(function(){
            jQuery("#tabla").select2("open");
        }, 100);
        jQuery("#tabla").change(function(){
            if(jQuery(this).val() === ""){
                jQuery("#crear").attr("disabled", "disabled");
            } else {
                jQuery("#crear").removeAttr("disabled");
            }
        });
    });
</script>