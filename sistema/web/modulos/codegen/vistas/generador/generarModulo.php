<?php 
$form = new CBForm([
    'id' => 'form-mod',
]);
$form->abrir();
?>
<div class="alert alert-info">
    El generador de módulos solo te generará el encarpetado básido que debe tener
    todo módulo, a partir de ahí debes darle la funcionalidad.
</div>

<div class="row">
    <div id="error-log" class="col-sm-6">
    
    </div>
</div>

<div class="col-sm-6">
    <div class="form-group">
        <label for="nombre-mod">Escriba el nombre que tendrá el módulo</label>
        <?php echo CBoot::textAddOn('', [
            'name' => 'nombre-mod', 
            'id' =>'nombre-mod',
            'pre' => CBoot::fa('cubes'),
            'autofocus' => true,
            ]
        ); ?>
    </div>
    <div class="form-group">
        <?php echo CBoot::boton(CBoot::fa('plus-circle') . ' Generar módulo', 'success', ['class' => 'btn-block', 'name' => 'crear-mod']); ?>
    </div>
</div>
<div class="row">        
    <div class="col-sm-12">
        <?php if(Sistema::apl()->mSesion->existeNotificacion("modCreado")): ?>
        <?php 
        $config = Sistema::apl()->mSesion->getNotificacion("modCreado");
        ?>
        <div class="alert alert-success">
            Se generó correctamente el módulo, copie la siguiente configuración en el 
            array de configuraciones, justo en el array de módulos. <br>
            
            <strong>Acceda al módulo así: <br>
                <a href="<?php echo Sistema::apl()->urlBase . "?r=".$config['nombre']; ?>" target="_blank">
                    <?php echo Sistema::apl()->urlBase . "?r=".$config['nombre']; ?>
                </a>
            </strong>
        </div>
            <?php echo $config['html'];?>
        <?php elseif(Sistema::apl()->mSesion->existeNotificacion("modErr")): ?>
        <div class="alert alert-danger">
            <?php echo Sistema::apl()->mSesion->getNotificacion("modErr");?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $form->cerrar(); ?>
<script>
    jQuery(function(){
        var alert = '<?php echo CBoot::alert('Ingrese un nombre', 'danger', [], true); ?>'; 
        jQuery("#form-mod").submit(function(){
            if(jQuery.trim(jQuery("#nombre-mod").val()) === ""){
                jQuery("#error-log").html(alert);                
                jQuery("#nombre-mod").focus();
                return false;
            } else {
                return true;
            }
        });
    });
</script>