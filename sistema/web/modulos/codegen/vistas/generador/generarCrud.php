<style>
    #archivos label.btn.btn-default{
        float: inherit;
        display: block;
        border-radius: 0px;
        margin: 0px 0px;
        position: relative;
    }
    #archivos label.btn.btn-default:first-child{
        border-radius: 5px 5px 0px 0px;
    }
    #archivos label.btn.btn-default:last-child{
        border-radius: 0px 0px 5px 5px;
    }
    #archivos .btn-group{
        width: 100%;
    }
</style>
<?php 
$f = new CBForm([
    'id' => 'crear_crud',
]);
$f->abrir();
?>
<?php if(Sistema::apl()->mSesion->existeNotificacion("crud")): ?>
<?php $not = Sistema::apl()->mSesion->getNotificacion("crud"); ?>
<div class="alert alert-<?php echo $not['error']? 'danger' : 'success'; ?>">
    <?php echo $not['msg']; ?>
</div>
<?php endif; ?>

<div class="col-sm-7">
    
    <div class="form-group">
        <label>¿De que tabla generará el modelo?</label>
        <?php echo CBoot::select('', $tablas, ['name' => 'tabla', 'id' => 'tabla', 'defecto' => 'Seleccione una tabla', 'data-select-two' => 'true']); ?>
    </div>
    <div class="form-group">
        <a class="btn btn-primary" role="button" data-toggle="collapse" href="#archivos" aria-expanded="false" aria-controls="archivos">
            Seleccionar archivos
        </a>
    </div>
    <div id="archivos" class="form-group collapse">
        <div class="panel panel-primary">
            <div class="panel-body">                
                <div class="btn-group" data-toggle="buttons">
                    <?php foreach($archivos AS $archivo): ?>
                    <label class="btn btn-default active">
                        <input name="archivos[]" type="checkbox" autocomplete="off" value="<?php echo $archivo?>" checked> <?php echo $archivo?>
                    </label>                       
                    <?php endforeach; ?>                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <?php echo CBoot::botonS(CBoot::fa('plus-circle') . ' Generar CRUD', ['id' => 'crear', 'name' => 'crear-crud', 'class' => 'btn-block', 'disabled' => 'disabled'])?>
    </div>
</div>
<div id="warning" class="col-sm-5" style="display:none">
    <?php echo CBoot::alert('<h3>Advertencia:</h3>Parece que algunos elementos del crud para esta tabla ya fueron generados, '
        . 'te recomiendo revisar. <br>También puedes <strong>elegir</strong> los archivos que se generarán<br><br>',
        'warning', [], false); ?>
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
                revisarCrud(jQuery(this).val());
            }
        });        
    });
    
    function revisarCrud(tabla){
        jQuery.ajax({
            type: 'POST',
            url: 'index.php?r=codegen/generador/crud',
            data: {
                ajxreq : true,
                tabla: tabla
            },
            success: function(obj){
                if(obj.existe === true){
                    jQuery("#warning").slideDown();
                } else {
                    jQuery("#warning").slideUp();
                }
                jQuery("#crear").removeAttr("disabled");
            }
        });
    }
</script>