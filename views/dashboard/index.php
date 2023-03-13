<?php include_once __DIR__ . "/header-dashboard.php"; ?>

    <?php if (count($proyectos)=== 0) { ?>
        <p class="no-proyectos">
            No hay proyectos
            <a href="/crear-proyecto">
            Crea tu primer proyecto!
            </a>
        </p>
        
    <?php } else { ?>
        <ul class="listado-proyectos">
        <?php foreach ($proyectos as $proyecto) { ?>
            <li class="proyecto">
                <a href="/proyecto?url=<?php echo $proyecto->url; ?>"><?php echo $proyecto->proyecto; ?></a>
                
                <form action="/eliminar-proyecto" method="post">
                    <input type="hidden" name="id" value="<?php echo $proyecto->id; ?>">
                    <input class="btn-eliminar-proyecto" type="submit" value="x">
                </form>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>

<?php include_once __DIR__ . "/footer-dashboard.php"; ?>