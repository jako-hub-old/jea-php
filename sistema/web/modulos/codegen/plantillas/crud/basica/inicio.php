<?php 
$columnas = $modelo->etiquetasAtributos();
$atributos = array_keys($columnas);
$pk = $modelo->getPk();
?>
<div class="page-header">
    <h3>Listado de <?php echo $nTabla; ?></h3>
</div>

<div class="col-sm-8">
    
    <div class="form-group">
        <a class="btn btn-primary" href="<?php echo "<?php echo Sistema::apl()->crearUrl(['" . lcfirst($nTabla) . "/crear'])?>"; ?>">Crear nuevo</a>
    </div>
    
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <?php foreach($columnas AS $col): ?>
                <th><?php echo $col; ?></th>
                <?php endforeach; ?>
                <th class="text-center">Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php echo "<?php foreach(\$modelos AS \$modelo): ?>\n"; ?>
            <tr>
                <?php foreach($atributos AS $col): ?>
                <td><?php echo "<?php echo \$modelo->$col; ?>" ?></td>
                <?php endforeach; ?>
                <td class="text-center">
                    <a href="<?php echo "<?php echo Sistema::apl()->crearUrl(['$nTabla/ver', 'id'=>\$modelo->$pk]); ?>"; ?>">
                        <?php echo "<?php echo CBoot::fa('eye'); ?>\n"; ?>
                    </a> 
                    <a href="<?php echo "<?php echo Sistema::apl()->crearUrl(['$nTabla/editar', 'id'=>\$modelo->$pk]); ?>"; ?>">
                        <?php echo "<?php echo CBoot::fa('pencil'); ?>\n"; ?>
                    </a> 
                    <a href="<?php echo "<?php echo Sistema::apl()->crearUrl(['$nTabla/eliminar', 'id'=>\$modelo->$pk]); ?>"; ?>">
                        <?php echo "<?php echo CBoot::fa('trash'); ?>\n"; ?>
                    </a> 
                </td>
            </tr>
            <?php echo "<?php endforeach; ?>\n"; ?>
        </tbody>
    </table>
</div>