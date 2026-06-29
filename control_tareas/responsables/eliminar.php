<?php
session_start();
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    $_SESSION['mensaje'] = "ID de responsable invalido.";
    $_SESSION['tipo'] = "error";
    header("Location: listar.php");
    exit();
}

try {
    $conexion->beginTransaction();
    
    $sql = "UPDATE tareas SET id_responsable = NULL WHERE id_responsable = ?";
    $consulta = $conexion->prepare($sql);
    $consulta->execute([$id]);
    
    $sql = "DELETE FROM responsables WHERE id_responsable = ?";
    $consulta = $conexion->prepare($sql);
    
    if ($consulta->execute([$id])) {
        $conexion->commit();
        $_SESSION['mensaje'] = "Responsable eliminado correctamente. Las tareas que le pertenecian quedaron sin responsable.";
        $_SESSION['tipo'] = "exito";
    } else {
        throw new Exception("Error al eliminar el responsable.");
    }
} catch (Exception $e) {
    $conexion->rollBack();
    $_SESSION['mensaje'] = "Error al eliminar el responsable: " . $e->getMessage();
    $_SESSION['tipo'] = "error";
}

header("Location: listar.php");
exit();
?>