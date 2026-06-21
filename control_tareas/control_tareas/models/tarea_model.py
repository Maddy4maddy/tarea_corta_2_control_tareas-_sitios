from config import obtener_conexion


class TareaModel:

    @staticmethod
    def obtener_todas():
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        sql = """
        SELECT
            t.*,
            CONCAT(r.nombre,' ',r.apellidos) AS responsable,
            g.nombre AS grupo
        FROM tareas t
        LEFT JOIN responsables r
            ON t.id_responsable = r.id_responsable
        LEFT JOIN grupos g
            ON t.id_grupo = g.id_grupo
        ORDER BY
            CASE
                WHEN t.estado='Finalizada' THEN 1
                ELSE 0
            END,
            t.id_tarea DESC
        """

        cursor.execute(sql)
        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas

    @staticmethod
    def obtener_por_id(id_tarea):
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        cursor.execute(
            """
            SELECT *
            FROM tareas
            WHERE id_tarea = %s
            """,
            (id_tarea,)
        )

        tarea = cursor.fetchone()

        cursor.close()
        conexion.close()

        return tarea

    @staticmethod
    def insertar(detalle, prioridad, fecha_limite, id_responsable, id_grupo):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            INSERT INTO tareas
            (
                detalle,
                prioridad,
                fecha_limite,
                id_responsable,
                id_grupo
            )
            VALUES (%s,%s,%s,%s,%s)
            """,
            (
                detalle,
                prioridad,
                fecha_limite,
                id_responsable,
                id_grupo
            )
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def actualizar(id_tarea, detalle, prioridad, fecha_limite, id_responsable, id_grupo):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            UPDATE tareas
            SET
                detalle=%s,
                prioridad=%s,
                fecha_limite=%s,
                id_responsable=%s,
                id_grupo=%s
            WHERE id_tarea=%s
            """,
            (
                detalle,
                prioridad,
                fecha_limite,
                id_responsable,
                id_grupo,
                id_tarea
            )
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def eliminar(id_tarea):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            DELETE FROM tareas
            WHERE id_tarea=%s
            """,
            (id_tarea,)
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def finalizar(id_tarea):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            UPDATE tareas
            SET
                estado='Finalizada',
                fecha_finalizacion=NOW()
            WHERE id_tarea=%s
            """,
            (id_tarea,)
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def reactivar(id_tarea):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            UPDATE tareas
            SET
                estado='Pendiente',
                fecha_finalizacion=NULL
            WHERE id_tarea=%s
            """,
            (id_tarea,)
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def cambiar_estado(id_tarea, nuevo_estado):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            UPDATE tareas
            SET estado=%s
            WHERE id_tarea=%s
            """,
            (nuevo_estado, id_tarea)
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def obtener_responsables():
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        cursor.execute("""
            SELECT *
            FROM responsables
            ORDER BY nombre
        """)

        responsables = cursor.fetchall()

        cursor.close()
        conexion.close()

        return responsables

    @staticmethod
    def obtener_grupos():
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        cursor.execute("""
            SELECT *
            FROM grupos
            ORDER BY nombre
        """)

        grupos = cursor.fetchall()

        cursor.close()
        conexion.close()

        return grupos
    

 # Este método es para mostrar tareas en el tablero con filtros aplicados (nazareth)
    
    @staticmethod
    def obtener_por_estado(estado):
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        cursor.execute("""
            SELECT
                t.*,
                CONCAT(r.nombre,' ',r.apellidos) AS responsable,
                g.nombre AS grupo
            FROM tareas t
            LEFT JOIN responsables r
                ON t.id_responsable = r.id_responsable
            LEFT JOIN grupos g
                ON t.id_grupo = g.id_grupo
            WHERE t.estado = %s
            ORDER BY t.id_tarea DESC
        """, (estado,))

        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas
    
    @staticmethod
    def obtener_con_filtros(estado=None, prioridad=None, responsable=None):

        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        sql = """
            SELECT
                t.*,
                CONCAT(r.nombre,' ',r.apellidos) AS responsable,
                g.nombre AS grupo
            FROM tareas t
            LEFT JOIN responsables r ON t.id_responsable = r.id_responsable
            LEFT JOIN grupos g ON t.id_grupo = g.id_grupo
            WHERE 1=1
        """

        params = []

        if estado:
            sql += " AND t.estado = %s"
            params.append(estado)

        if prioridad:
            sql += " AND t.prioridad = %s"
            params.append(prioridad)

        if responsable:
            sql += " AND t.id_responsable = %s"
            params.append(responsable)

        sql += " ORDER BY t.id_tarea DESC"

        cursor.execute(sql, tuple(params))
        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas
    
    @staticmethod
    def finalizar(id_tarea):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute("""
            UPDATE tareas
            SET estado='Finalizada',
                fecha_finalizacion=NOW()
            WHERE id_tarea=%s
        """, (id_tarea,))

        conexion.commit()
        cursor.close()
        conexion.close()

    @staticmethod
    def obtener_todas_filtradas(estado=None, prioridad=None, fecha=None):

        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        sql = """
        SELECT
            t.*,
            CONCAT(r.nombre,' ',r.apellidos) AS responsable,
            g.nombre AS grupo
        FROM tareas t
        LEFT JOIN responsables r ON t.id_responsable = r.id_responsable
        LEFT JOIN grupos g ON t.id_grupo = g.id_grupo
        WHERE 1=1
        """

        params = []

        if estado:
            sql += " AND t.estado = %s"
            params.append(estado)

        if prioridad:
            sql += " AND t.prioridad = %s"
            params.append(prioridad)

        # 🔥 FILTRO FECHA
        if fecha:
            sql += " AND t.fecha_limite <= %s"
            params.append(fecha)

        sql += """
        ORDER BY
            CASE WHEN t.estado='Finalizada' THEN 1 ELSE 0 END,
            t.id_tarea DESC
        """

        cursor.execute(sql, params)
        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas