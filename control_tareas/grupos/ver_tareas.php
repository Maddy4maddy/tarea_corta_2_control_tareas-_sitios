<?php
require_once "../config/conexion.php";

$titulo_pagina = "Tareas del grupo";
$subtitulo_pagina = "Consulta de tareas asociadas a un grupo";
$pagina_activa = "grupos";
$css_pagina = "grupos/ver_tareas.css";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó el grupo que desea consultar.";
    header("Location: listar.php");
    exit;
}

$id_grupo = $_GET["id"];

$consultaGrupo = $conexion->prepare("
    SELECT id_grupo, nombre
    FROM grupos
    WHERE id_grupo = :id_grupo
");
$consultaGrupo->bindParam(":id_grupo", $id_grupo);
$consultaGrupo->execute();
$grupo = $consultaGrupo->fetch();

if (!$grupo) {
    $_SESSION["error"] = "El grupo seleccionado no existe.";
    header("Location: listar.php");
    exit;
}

$consultaTareas = $conexion->prepare("
    SELECT 
        t.id_tarea,
        t.detalle,
        t.prioridad,
        t.fecha_limite,
        t.estado,
        t.fecha_finalizacion,
        r.nombre AS nombre_responsable,
        r.apellidos AS apellidos_responsable
    FROM tareas t
    LEFT JOIN responsables r ON t.id_responsable = r.id_responsable
    WHERE t.id_grupo = :id_grupo
    ORDER BY 
        CASE WHEN t.estado = 'Finalizada' THEN 1 ELSE 0 END,
        t.fecha_limite IS NULL,
        t.fecha_limite ASC,
        t.id_tarea DESC
");
$consultaTareas->bindParam(":id_grupo", $id_grupo);
$consultaTareas->execute();
$tareas = $consultaTareas->fetchAll();

$total = count($tareas);
$pendientes = 0;
$progreso = 0;
$bloqueadas = 0;
$finalizadas = 0;

foreach ($tareas as $tarea) {
    if ($tarea["estado"] === "Pendiente") {
        $pendientes++;
    } elseif ($tarea["estado"] === "En progreso") {
        $progreso++;
    } elseif ($tarea["estado"] === "Bloqueada") {
        $bloqueadas++;
    } elseif ($tarea["estado"] === "Finalizada") {
        $finalizadas++;
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="resumen-grupo">
    <h2><?php echo htmlspecialchars($grupo["nombre"]); ?></h2>
    <p>Tareas asociadas a este grupo.</p>

    <div class="estadisticas">
        <div class="estadistica">
            <h3><?php echo $total; ?></h3>
            <span>Total</span>
        </div>

        <div class="estadistica">
            <h3><?php echo $pendientes; ?></h3>
            <span>Pendientes</span>
        </div>

        <div class="estadistica">
            <h3><?php echo $progreso; ?></h3>
            <span>En progreso</span>
        </div>

        <div class="estadistica">
            <h3><?php echo $finalizadas; ?></h3>
            <span>Finalizadas</span>
        </div>
    </div>
</div>

<div class="tabla-contenedor">
    <?php if (count($tareas) > 0): ?>
        <table class="tabla-tareas-grupo">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th>Responsable</th>
                    <th>Prioridad</th>
                    <th>Fecha límite</th>
                    <th>Estado</th>
                    <th>Fecha finalización</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tareas as $tarea): ?>
                    <?php
                        $claseEstado = strtolower(str_replace(" ", "-", $tarea["estado"]));
                        $clasePrioridad = strtolower($tarea["prioridad"]);

                        $responsable = "Sin responsable asignado";

                        if (!empty($tarea["nombre_responsable"])) {
                            $responsable = $tarea["nombre_responsable"] . " " . $tarea["apellidos_responsable"];
                        }
                    ?>

                    <tr>
                        <td>
                            <span class="detalle-tarea <?php echo $tarea["estado"] === "Finalizada" ? "tarea-finalizada" : ""; ?>">
                                <?php echo htmlspecialchars($tarea["detalle"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($responsable === "Sin responsable asignado"): ?>
                                <span class="texto-suave">Sin responsable asignado</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($responsable); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="prioridad prioridad-<?php echo $clasePrioridad; ?>">
                                <?php echo htmlspecialchars($tarea["prioridad"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo $tarea["fecha_limite"] ? htmlspecialchars($tarea["fecha_limite"]) : "Sin fecha"; ?>
                        </td>

                        <td>
                            <span class="estado estado-<?php echo $claseEstado; ?>">
                                <?php echo htmlspecialchars($tarea["estado"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo $tarea["fecha_finalizacion"] ? htmlspecialchars($tarea["fecha_finalizacion"]) : "Sin finalizar"; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="mensaje-vacio">
            <h3>Este grupo no tiene tareas asociadas</h3>
            <p>Puede asociar tareas desde la opción de crear o editar tareas.</p>
        </div>
    <?php endif; ?>
</div>

<div class="volver">
    <a href="listar.php" class="btn btn-secundario">Volver al listado de grupos</a>
</div>

<?php include "../includes/footer.php"; ?>