<?php 
$columnas = $modelo->etiquetasAtributos();
$pk = $modelo->getPk();
?>
<div class="col-sm-6">
    <div class="page-header">
        <h3>Ver <?php echo $nTabla; ?></h3>
    </div>
    <div class="form-group">
        <a class="btn btn-primary" href="<?php echo Sistema::apl()->crearUrl([lcfirst($nTabla) . '/inicio'])?>">Listar</a>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading text-center">
            Ver detalles
        </div>
        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <?php foreach ($columnas AS $col=>$et): ?>
                <tr>
                    <th><?php echo "<?php echo \$modelo->obtenerEtiqueta('$col') ?>"; ?></th>
                    <td><?php echo "<?php echo \$modelo->$col; ?>"?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>