<?php
session_start();
require_once "../config/conexion.php";

$titulo_pagina = "Editar responsable";
$subtitulo_pagina = "Modifica los datos del responsable seleccionado";
$pagina_activa = "responsables";
$css_pagina = "responsables/editar.css";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    $_SESSION['mensaje'] = "ID de responsable invalido.";
    $_SESSION['tipo'] = "error";
    header("Location: listar.php");
    exit();
}

$sql = "SELECT * FROM responsables WHERE id_responsable = ?";
$consulta = $conexion->prepare($sql);
$consulta->execute([$id]);
$responsable = $consulta->fetch();

if (!$responsable) {
    $_SESSION['mensaje'] = "Responsable no encontrado.";
    $_SESSION['tipo'] = "error";
    header("Location: listar.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identificacion = trim($_POST['identificacion']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);

    if (empty($identificacion) || empty($nombre) || empty($apellidos)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $sql = "SELECT id_responsable FROM responsables WHERE identificacion = ? AND id_responsable != ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$identificacion, $id]);
        
        if ($consulta->rowCount() > 0) {
            $error = "La identificacion ingresada ya esta registrada por otro responsable.";
        } else {
            $sql = "UPDATE responsables SET identificacion = ?, nombre = ?, apellidos = ? WHERE id_responsable = ?";
            $consulta = $conexion->prepare($sql);
            
            if ($consulta->execute([$identificacion, $nombre, $apellidos, $id])) {
                $_SESSION['mensaje'] = "Responsable actualizado correctamente.";
                $_SESSION['tipo'] = "exito";
                header("Location: listar.php");
                exit();
            } else {
                $error = "Error al actualizar el responsable.";
            }
        }
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="contenido-principal">
    <div class="barra-superior">
        <h2><?php echo htmlspecialchars($titulo_pagina); ?></h2>
        <p><?php echo htmlspecialchars($subtitulo_pagina); ?></p>
    </div>

    <div class="contenedor">
        <div class="form-card">
            <div class="form-header">
                <h3>Editar responsable</h3>
                <p>Actualiza la informacion del responsable.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alerta alerta-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="formulario-tarea">
                    <div class="fila-formulario">
                        <div class="campo campo-completo">
                            <label for="identificacion">Identificacion *</label>
                            <input type="text" id="identificacion" name="identificacion" required 
                                   value="<?php echo htmlspecialchars($responsable['identificacion']); ?>">
                        </div>
                    </div>
                    <div class="fila-formulario">
                        <div class="campo">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" required 
                                   value="<?php echo htmlspecialchars($responsable['nombre']); ?>">
                        </div>
                        <div class="campo">
                            <label for="apellidos">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" required 
                                   value="<?php echo htmlspecialchars($responsable['apellidos']); ?>">
                        </div>
                    </div>
                    <div class="botones-formulario">
                        <button type="submit" class="btn btn-principal">Actualizar responsable</button>
                        <a href="listar.php" class="btn btn-secundario">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>