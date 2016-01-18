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
<div class="col-sm-7">
    
    <div class="form-group">
        <label>¿De que tabla generará el modelo?</label>
        <?php echo CBoot::select('', $tablas, ['name' => 'tabla', 'id' => 'tabla', 'defecto' => 'Seleccione una tabla', 'data-select-two' => 'true']); ?>
    </div>
<!--    <div class="form-group">
        <label>¿Sobreescribir el modelo si ya existe?</label>
        <div class="btn-group" data-toggle="buttons">
            <label class="btn btn-primary active">
                <input type="radio" name="sobreescribir" id="override-yes" autocomplete="off" value="1" checked> Si
            </label>        
            <label class="btn btn-default">
                <input type="radio" name="sobreescribir" id="override-no" autocomplete="off" value="0"> No
            </label>
        </div>
    </div>-->
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
<div id="warning" class="col-sm-5">
    <?php echo CBoot::alert('Ya se realizó un CRUD para esta tabla, si genera el crud de nuevo se '
        . 'sobreescribirá el anterior<br><br>' 
        . '<strong>Nota: </strong> <br>'
        . 'Puede elegir que archivos se generarán', 'warning', [], true); ?>
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