<div class="contenedor reestablecer">
    <?php include_once __DIR__.'/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <p class="descripcion">
            Reestablecer contraseña
        </p>

        <?php include_once __DIR__.'/../templates/alertas.php'; ?>

        <?php if ($mostrar) { ?> 
        <form method="POST" class="formulario">
        <div class="campo">
                <label for="password">Password: </label>
                <input type="password" id="password" 
                placeholder="Tu Pass" name="password">
            </div>

            <input type="submit" class="boton" value="Confirmar">
        </form>
        <?php } ?>

        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Crea una</a>
        </div>
    </div>

</div>