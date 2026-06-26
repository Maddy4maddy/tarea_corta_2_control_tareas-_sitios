<?php if (isset($_SESSION["mensaje"])): ?>
    <div class="alerta alerta-exito">
        <?php echo htmlspecialchars($_SESSION["mensaje"]); ?>
    </div>

    <?php unset($_SESSION["mensaje"]); ?>
<?php endif; ?>

<?php if (isset($_SESSION["error"])): ?>
    <div class="alerta alerta-error">
        <?php echo htmlspecialchars($_SESSION["error"]); ?>
    </div>

    <?php unset($_SESSION["error"]); ?>
<?php endif; ?>