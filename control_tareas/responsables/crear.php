<?php
session_start();
require_once "../config/conexion.php";

$titulo_pagina = "Crear responsable";
$subtitulo_pagina = "Registra un nuevo responsable para asignarlo a tareas";
$pagina_activa = "responsables";
$css_pagina = "responsables/crear.css";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identificacion = trim($_POST['identificacion']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);

    if (empty($identificacion) || empty($nombre) || empty($apellidos)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $sql = "SELECT id_responsable FROM responsables WHERE identificacion = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$identificacion]);
        
        if ($consulta->rowCount() > 0) {
            $error = "La identificacion ingresada ya esta registrada.";
        } else {
            $sql = "INSERT INTO responsables (identificacion, nombre, apellidos) VALUES (?, ?, ?)";
            $consulta = $conexion->prepare($sql);
            
            if ($consulta->execute([$identificacion, $nombre, $apellidos])) {
                $_SESSION['mensaje'] = "Responsable creado correctamente.";
                $_SESSION['tipo'] = "exito";
                header("Location: listar.php");
                exit();
            } else {
                $error = "Error al crear el responsable.";
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
                <h3>Nuevo responsable</h3>
                <p>Ingresa los datos del responsable.</p>
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
                            <input type="text" id="identificacion" name="identificacion" 
                                   placeholder="Ej: 1-1234-5678" required 
                                   value="<?php echo isset($_POST['identificacion']) ? htmlspecialchars($_POST['identificacion']) : ''; ?>">
                        </div>
                    </div>
                    <div class="fila-formulario">
                        <div class="campo">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" 
                                   placeholder="Ej: Juan" required 
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        </div>
                        <div class="campo">
                            <label for="apellidos">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" 
                                   placeholder="Ej: Perez Gomez" required 
                                   value="<?php echo isset($_POST['apellidos']) ? htmlspecialchars($_POST['apellidos']) : ''; ?>">
                        </div>
                    </div>
                    <div class="botones-formulario">
                        <button type="submit" class="btn btn-principal">Guardar responsable</button>
                        <a href="listar.php" class="btn btn-secundario">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>