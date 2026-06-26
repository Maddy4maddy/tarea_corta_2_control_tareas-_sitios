DROP DATABASE IF EXISTS control_tareas;

CREATE DATABASE control_tareas
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE control_tareas;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS tareas;
DROP TABLE IF EXISTS responsables;
DROP TABLE IF EXISTS grupos;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE grupos (
  id_grupo INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO grupos (id_grupo, nombre) VALUES
(1, 'Universidad'),
(2, 'Trabajo'),
(3, 'Personal');

CREATE TABLE responsables (
  id_responsable INT NOT NULL AUTO_INCREMENT,
  identificacion VARCHAR(50) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(150) NOT NULL,
  PRIMARY KEY (id_responsable)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO responsables (id_responsable, identificacion, nombre, apellidos) VALUES
(1, '208400050', 'Angie', 'Romero Ceciliano'),
(2, '202220222', 'Adrián', 'Solano Vargas'),
(3, '303330333', 'Mauricio', 'Madriz Ceciliano');

CREATE TABLE tareas (
  id_tarea INT NOT NULL AUTO_INCREMENT,
  detalle TEXT NOT NULL,
  prioridad ENUM('Baja','Media','Alta') NOT NULL,
  fecha_limite DATE DEFAULT NULL,
  estado ENUM('Pendiente','En progreso','Bloqueada','Finalizada') NOT NULL DEFAULT 'Pendiente',
  fecha_finalizacion DATETIME DEFAULT NULL,
  id_responsable INT DEFAULT NULL,
  id_grupo INT DEFAULT NULL,
  PRIMARY KEY (id_tarea),
  KEY idx_tareas_responsable (id_responsable),
  KEY idx_tareas_grupo (id_grupo),
  CONSTRAINT fk_tareas_responsables
    FOREIGN KEY (id_responsable)
    REFERENCES responsables (id_responsable)
    ON DELETE SET NULL,
  CONSTRAINT fk_tareas_grupos
    FOREIGN KEY (id_grupo)
    REFERENCES grupos (id_grupo)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tareas 
(id_tarea, detalle, prioridad, fecha_limite, estado, fecha_finalizacion, id_responsable, id_grupo) 
VALUES
(1, 'Realizar documentación del proyecto', 'Alta', '2026-06-22', 'Pendiente', NULL, 1, 3),
(2, 'Revisar diseño de base de datos', 'Media', '2026-06-21', 'Finalizada', '2026-06-18 16:24:04', 2, 1),
(3, 'Comprar materiales pendientes', 'Baja', NULL, 'Pendiente', NULL, NULL, 3),
(4, 'Corregir errores del sistema', 'Alta', '2026-06-23', 'Bloqueada', NULL, 3, 2),
(5, 'Análisis de Requerimientos', 'Alta', '2026-07-12', 'En progreso', NULL, 1, 1);