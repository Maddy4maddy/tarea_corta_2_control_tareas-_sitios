<?php
require_once "../config/conexion.php";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó la tarea que desea finalizar.";
    header("Location: listar.php");
    exit;
}

$id_tarea = $_GET["id"];

$consulta = $conexion->prepare("SELECT estado FROM tareas WHERE id_tarea = :id_tarea");
$consulta->bindParam(":id_tarea", $id_tarea);
$consulta->execute();
$tarea = $consulta->fetch();

if (!$tarea) {
    $_SESSION["error"] = "La tarea seleccionada no existe.";
    header("Location: listar.php");
    exit;
}

if ($tarea["estado"] !== "En progreso") {
    $_SESSION["error"] = "Solo una tarea en estado En progreso puede marcarse como Finalizada.";
    header("Location: listar.php");
    exit;
}

$sql = "
    UPDATE tareas
    SET estado = 'Finalizada',
        fecha_finalizacion = NOW()
    WHERE id_tarea = :id_tarea
";

$actualizar = $conexion->prepare($sql);
$actualizar->bindParam(":id_tarea", $id_tarea);
$actualizar->execute();

$_SESSION["mensaje"] = "La tarea fue marcada como finalizada.";
header("Location: listar.php");
exit;