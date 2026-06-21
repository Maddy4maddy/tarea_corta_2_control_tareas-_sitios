from database.db_manager import DatabaseManager

class ResponsableModel:
    def __init__(self):
        self.db = DatabaseManager()
    
    def obtener_todos(self):
        query = "SELECT * FROM responsables ORDER BY apellidos, nombre"
        return self.db.execute_query(query)
    
    def obtener_por_id(self, responsable_id):
        query = "SELECT * FROM responsables WHERE id_responsable = %s"
        result = self.db.execute_query(query, (responsable_id,))
        return result[0] if result else None
    
    def crear(self, nombre, apellidos, identificacion):
        query = """
            INSERT INTO responsables (nombre, apellidos, identificacion) 
            VALUES (%s, %s, %s)
        """
        return self.db.execute_insert(query, (nombre, apellidos, identificacion))
    
    def actualizar(self, responsable_id, nombre, apellidos, identificacion):
        query = """
            UPDATE responsables 
            SET nombre = %s, apellidos = %s, identificacion = %s 
            WHERE id_responsable = %s
        """
        return self.db.execute_update(query, (nombre, apellidos, identificacion, responsable_id))
    
    def eliminar(self, responsable_id):
        # Primero actualizar tareas para quitar la referencia
        query_tareas = """
            UPDATE tareas 
            SET id_responsable = NULL 
            WHERE id_responsable = %s
        """
        self.db.execute_update(query_tareas, (responsable_id,))
        
        # Luego eliminar el responsable
        query = "DELETE FROM responsables WHERE id_responsable = %s"
        return self.db.execute_delete(query, (responsable_id,))
    
    def obtener_tareas(self, responsable_id):
        query = """
            SELECT t.*, g.nombre as grupo_nombre 
            FROM tareas t
            LEFT JOIN grupos g ON t.id_grupo = g.id_grupo
            WHERE t.id_responsable = %s 
            ORDER BY t.fecha_creacion DESC
        """
        return self.db.execute_query(query, (responsable_id,))
    
    def existe_identificacion(self, identificacion, excluir_id=None):
        if excluir_id:
            query = """
                SELECT COUNT(*) as count FROM responsables 
                WHERE identificacion = %s AND id_responsable != %s
            """
            result = self.db.execute_query(query, (identificacion, excluir_id))
        else:
            query = """
                SELECT COUNT(*) as count FROM responsables 
                WHERE identificacion = %s
            """
            result = self.db.execute_query(query, (identificacion,))
        
        return result[0]['count'] > 0 if result else False
    
    def contar_tareas(self, responsable_id):
        query = """
            SELECT COUNT(*) as total FROM tareas 
            WHERE id_responsable = %s
        """
        result = self.db.execute_query(query, (responsable_id,))
        return result[0]['total'] if result else 0