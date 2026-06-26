<?php
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["error"] = "Solicitud no válida.";
    header("Location: listar.php");
    exit;
}

if (
    !isset($_POST["id_tarea"]) ||
    !isset($_POST["estado_actual"]) ||
    !isset($_POST["nuevo_estado"])
) {
    $_SESSION["error"] = "Faltan datos para cambiar el estado.";
    header("Location: listar.php");
    exit;
}

$id_tarea = $_POST["id_tarea"];
$estado_actual_formulario = $_POST["estado_actual"];
$nuevo_estado = $_POST["nuevo_estado"];

$estados_validos = ["Pendiente", "En progreso", "Bloqueada", "Finalizada"];

if (!in_array($nuevo_estado, $estados_validos)) {
    $_SESSION["error"] = "El estado seleccionado no es válido.";
    header("Location: listar.php");
    exit;
}

$consulta = $conexion->prepare("SELECT estado FROM tareas WHERE id_tarea = :id_tarea");
$consulta->bindParam(":id_tarea", $id_tarea);
$consulta->execute();
$tarea = $consulta->fetch();

if (!$tarea) {
    $_SESSION["error"] = "La tarea seleccionada no existe.";
    header("Location: listar.php");
    exit;
}

$estado_actual = $tarea["estado"];

$cambios_validos = [
    "Pendiente" => ["En progreso"],
    "En progreso" => ["Pendiente", "Bloqueada", "Finalizada"],
    "Bloqueada" => ["En progreso"],
    "Finalizada" => []
];

if (!in_array($nuevo_estado, $cambios_validos[$estado_actual])) {
    $_SESSION["error"] = "El cambio de estado no es válido.";
    header("Location: listar.php");
    exit;
}

if ($nuevo_estado === "Finalizada") {
    $sql = "
        UPDATE tareas
        SET estado = 'Finalizada',
            fecha_finalizacion = NOW()
        WHERE id_tarea = :id_tarea
    ";
} else {
    $sql = "
        UPDATE tareas
        SET estado = :nuevo_estado,
            fecha_finalizacion = NULL
        WHERE id_tarea = :id_tarea
    ";
}

$actualizar = $conexion->prepare($sql);

if ($nuevo_estado !== "Finalizada") {
    $actualizar->bindParam(":nuevo_estado", $nuevo_estado);
}

$actualizar->bindParam(":id_tarea", $id_tarea);
$actualizar->execute();

$_SESSION["mensaje"] = "El estado de la tarea fue actualizado correctamente.";
header("Location: listar.php");
exit;