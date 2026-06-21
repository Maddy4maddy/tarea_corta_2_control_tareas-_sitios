from config import obtener_conexion


class GrupoModel:

    @staticmethod
    def obtener_todos():
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

    @staticmethod
    def insertar(nombre):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            INSERT INTO grupos(nombre)
            VALUES(%s)
            """,
            (nombre,)
        )

        conexion.commit()

        id_grupo_creado = cursor.lastrowid

        cursor.close()
        conexion.close()

        return id_grupo_creado

    @staticmethod
    def eliminar(id_grupo):
        conexion = obtener_conexion()
        cursor = conexion.cursor()

        cursor.execute(
            """
            DELETE FROM grupos
            WHERE id_grupo=%s
            """,
            (id_grupo,)
        )

        conexion.commit()

        cursor.close()
        conexion.close()

    @staticmethod
    def obtener_tareas_grupo(id_grupo):
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        sql = """
        SELECT
            t.*,
            CONCAT(r.nombre,' ',r.apellidos) AS responsable
        FROM tareas t
        LEFT JOIN responsables r
            ON t.id_responsable = r.id_responsable
        WHERE t.id_grupo=%s
        """

        cursor.execute(sql, (id_grupo,))
        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas

    @staticmethod
    def obtener_tareas_pendientes():
        conexion = obtener_conexion()
        cursor = conexion.cursor(dictionary=True)

        cursor.execute("""
            SELECT *
            FROM tareas
            WHERE estado='Pendiente'
        """)

        tareas = cursor.fetchall()

        cursor.close()
        conexion.close()

        return tareas