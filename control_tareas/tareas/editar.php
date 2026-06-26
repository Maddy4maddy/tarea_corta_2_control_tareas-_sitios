<?php
require_once "../config/conexion.php";

$titulo_pagina = "Editar tarea";
$subtitulo_pagina = "Modificar los datos de una tarea";
$pagina_activa = "tareas";
$css_pagina = "tareas/editar.css";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó la tarea que desea editar.";
    header("Location: listar.php");
    exit;
}

$id_tarea = $_GET["id"];

$consultaTarea = $conexion->prepare("SELECT * FROM tareas WHERE id_tarea = :id_tarea");
$consultaTarea->bindParam(":id_tarea", $id_tarea);
$consultaTarea->execute();
$tarea = $consultaTarea->fetch();

if (!$tarea) {
    $_SESSION["error"] = "La tarea seleccionada no existe.";
    header("Location: listar.php");
    exit;
}

$consultaResponsables = $conexion->prepare("SELECT id_responsable, nombre, apellidos FROM responsables ORDER BY nombre, apellidos");
$consultaResponsables->execute();
$responsables = $consultaResponsables->fetchAll();

$consultaGrupos = $conexion->prepare("SELECT id_grupo, nombre FROM grupos ORDER BY nombre");
$consultaGrupos->execute();
$grupos = $consultaGrupos->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $detalle = trim($_POST["detalle"]);
    $prioridad = $_POST["prioridad"];
    $fecha_limite = !empty($_POST["fecha_limite"]) ? $_POST["fecha_limite"] : null;
    $id_responsable = !empty($_POST["id_responsable"]) ? $_POST["id_responsable"] : null;
    $id_grupo = !empty($_POST["id_grupo"]) ? $_POST["id_grupo"] : null;

    if ($detalle === "" || $prioridad === "") {
        $_SESSION["error"] = "Debe completar el detalle y la prioridad de la tarea.";
    } else {
        $sql = "
            UPDATE tareas
            SET 
                detalle = :detalle,
                prioridad = :prioridad,
                fecha_limite = :fecha_limite,
                id_responsable = :id_responsable,
                id_grupo = :id_grupo
            WHERE id_tarea = :id_tarea
        ";

        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(":detalle", $detalle);
        $consulta->bindParam(":prioridad", $prioridad);
        $consulta->bindParam(":fecha_limite", $fecha_limite);
        $consulta->bindParam(":id_responsable", $id_responsable);
        $consulta->bindParam(":id_grupo", $id_grupo);
        $consulta->bindParam(":id_tarea", $id_tarea);
        $consulta->execute();

        $_SESSION["mensaje"] = "La tarea fue actualizada correctamente.";
        header("Location: listar.php");
        exit;
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="titulo-pagina">Editar tarea</h2>
        <p class="subtitulo-pagina">Puede cambiar el detalle, responsable, grupo, prioridad y fecha límite.</p>
    </div>

    <form method="POST" class="formulario-tarea">
        <div class="campo campo-completo">
            <label for="detalle">Detalle de la tarea</label>
            <textarea id="detalle" name="detalle" required><?php echo htmlspecialchars($tarea["detalle"]); ?></textarea>
        </div>

        <div class="fila-formulario">
            <div class="campo">
                <label for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad" required>
                    <option value="">Seleccione una prioridad</option>
                    <option value="Baja" <?php echo $tarea["prioridad"] === "Baja" ? "selected" : ""; ?>>Baja</option>
                    <option value="Media" <?php echo $tarea["prioridad"] === "Media" ? "selected" : ""; ?>>Media</option>
                    <option value="Alta" <?php echo $tarea["prioridad"] === "Alta" ? "selected" : ""; ?>>Alta</option>
                </select>
            </div>

            <div class="campo">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo htmlspecialchars($tarea["fecha_limite"] ?? ""); ?>">
            </div>
        </div>

        <div class="fila-formulario">
            <div class="campo">
                <label for="id_responsable">Responsable</label>
                <select id="id_responsable" name="id_responsable">
                    <option value="">Sin responsable asignado</option>

                    <?php foreach ($responsables as $responsable): ?>
                        <option value="<?php echo $responsable["id_responsable"]; ?>"
                            <?php echo $tarea["id_responsable"] == $responsable["id_responsable"] ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($responsable["nombre"] . " " . $responsable["apellidos"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="id_grupo">Grupo</label>
                <select id="id_grupo" name="id_grupo">
                    <option value="">Sin grupo</option>

                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?php echo $grupo["id_grupo"]; ?>"
                            <?php echo $tarea["id_grupo"] == $grupo["id_grupo"] ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($grupo["nombre"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="estado-actual">
            Estado actual de la tarea: <strong><?php echo htmlspecialchars($tarea["estado"]); ?></strong>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="btn btn-principal">Guardar cambios</button>
            <a href="listar.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>