<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($titulo_pagina)) {
    $titulo_pagina = "Control de Tareas";
}

if (!isset($subtitulo_pagina)) {
    $subtitulo_pagina = "Sistema web para el control de tareas";
}

if (!isset($pagina_activa)) {
    $pagina_activa = "";
}

$base_url = "/control_tareas";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>

    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/global.css?v=<?php echo time(); ?>">

    <?php if (isset($css_pagina)): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/<?php echo $css_pagina; ?>?v=<?php echo time(); ?>">
    <?php endif; ?>
</head>
<body>