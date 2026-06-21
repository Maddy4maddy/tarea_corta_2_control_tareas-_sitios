import mysql.connector
from mysql.connector import Error

class DatabaseManager:
    def __init__(self):
        self.connection = None
    
    def get_connection(self):
        try:
            if not self.connection or not self.connection.is_connected():
                self.connection = mysql.connector.connect(
                    host="localhost",
                    user="root",
                    password="1234",
                    database="control_tareas"
                )
            return self.connection
        except Error as e:
            print(f"Error de conexión a la base de datos: {e}")
            return None
    
    def execute_query(self, query, params=None):
        conn = self.get_connection()
        if not conn:
            return None
        
        try:
            cursor = conn.cursor(dictionary=True)
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            result = cursor.fetchall()
            cursor.close()
            return result
        except Error as e:
            print(f"Error en la consulta: {e}")
            return None
    
    def execute_insert(self, query, params=None):
        conn = self.get_connection()
        if not conn:
            return None
        
        try:
            cursor = conn.cursor()
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            conn.commit()
            last_id = cursor.lastrowid
            cursor.close()
            return last_id
        except Error as e:
            print(f"Error en la inserción: {e}")
            return None
    
    def execute_update(self, query, params=None):
        conn = self.get_connection()
        if not conn:
            return False
        
        try:
            cursor = conn.cursor()
            if params:
                cursor.execute(query, params)
            else:
                cursor.execute(query)
            conn.commit()
            affected_rows = cursor.rowcount
            cursor.close()
            return affected_rows > 0
        except Error as e:
            print(f"Error en la actualización: {e}")
            return False
    
    def execute_delete(self, query, params=None):
        return self.execute_update(query, params)
    
    def close(self):
        if self.connection and self.connection.is_connected():
            self.connection.close()