<?php
require_once "../config/conexion.php";

$titulo_pagina = "Crear tarea";
$subtitulo_pagina = "Registro de una nueva tarea";
$pagina_activa = "tareas";
$css_pagina = "tareas/crear.css";

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
            INSERT INTO tareas 
            (detalle, prioridad, fecha_limite, estado, fecha_finalizacion, id_responsable, id_grupo)
            VALUES 
            (:detalle, :prioridad, :fecha_limite, 'Pendiente', NULL, :id_responsable, :id_grupo)
        ";

        $consulta = $conexion->prepare($sql);

        $consulta->bindParam(":detalle", $detalle);
        $consulta->bindParam(":prioridad", $prioridad);
        $consulta->bindParam(":fecha_limite", $fecha_limite);
        $consulta->bindParam(":id_responsable", $id_responsable);
        $consulta->bindParam(":id_grupo", $id_grupo);

        $consulta->execute();

        $_SESSION["mensaje"] = "La tarea fue creada correctamente con estado Pendiente.";
        header("Location: listar.php");
        exit;
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="titulo-pagina">Nueva tarea</h2>
        <p class="subtitulo-pagina">Complete los datos solicitados para registrar una tarea.</p>
    </div>

    <form method="POST" class="formulario-tarea">
        <div class="campo campo-completo">
            <label for="detalle">Detalle de la tarea</label>
            <textarea id="detalle" name="detalle" required></textarea>
        </div>

        <div class="fila-formulario">
            <div class="campo">
                <label for="prioridad">Prioridad</label>
                <select id="prioridad" name="prioridad" required>
                    <option value="">Seleccione una prioridad</option>
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                </select>
            </div>

            <div class="campo">
                <label for="fecha_limite">Fecha límite</label>
                <input type="date" id="fecha_limite" name="fecha_limite">
            </div>
        </div>

        <div class="fila-formulario">
            <div class="campo">
                <label for="id_responsable">Responsable</label>
                <select id="id_responsable" name="id_responsable">
                    <option value="">Sin responsable asignado</option>

                    <?php foreach ($responsables as $responsable): ?>
                        <option value="<?php echo $responsable["id_responsable"]; ?>">
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
                        <option value="<?php echo $grupo["id_grupo"]; ?>">
                            <?php echo htmlspecialchars($grupo["nombre"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="nota-formulario">
            Al crear la tarea, el estado inicial será automáticamente <strong>Pendiente</strong>.
        </div>

        <div class="botones-formulario">
            <button type="submit" class="btn btn-principal">Guardar tarea</button>
            <a href="listar.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>