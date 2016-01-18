<div class="page-header">
    <h3>Editar <?php echo $nTabla ?></h3>
</div>

<div class="col-sm-6">    
    <?php echo "<?php echo \$this->mostrarVistaP('_formulario', ['modelo' => \$modelo]); ?>\n"; ?>
</div>
<div class="col-sm-offset-2 col-sm-4">
    <div class="panel panel-primary">
        <div class="panel-heading text-center">
            Opciones
        </div>
        <?php echo "<?php echo CBoot::linkedList([
            ['texto' => 'Listar', 'opciones' => ['url' => ['" . lcfirst($nTabla) . "/inicio']]],
            ['texto' => 'Crear nuevo', 'opciones' => ['url' => ['" . lcfirst($nTabla) . "/crear']]],
        ], ['class' => 'text-center']); ?>\n"; ?>
    </div>
</div>
