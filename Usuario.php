<?php
class Usuario {
    private $conn;
    private $tabla = 'usuarios'; // Asegúrate de cambiar esto por el nombre de tu tabla real

    // Propiedades del usuario
    public $id;
    public $correo;
    public $contrasena;
    public $token;
    public $rol;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar un nuevo usuario
    public function registrar() {
        $query = "INSERT INTO usuarios (correo, contrasena, rol) VALUES (:correo, :contrasena, :rol)";
    
        $stmt = $this->conn->prepare($query);
    
        // Hashear la contraseña
        $this->contrasena = password_hash($this->contrasena, PASSWORD_BCRYPT);
    
        // Bind de parámetros
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':contrasena', $this->contrasena);
        $stmt->bindParam(':rol', $this->rol);
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    

    // Login de usuario
    public function login() {
        $query = "SELECT id, contrasena, rol FROM " . $this->tabla . " WHERE correo = :correo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->rol = $row['rol'];

            // Verificar contraseña
            if (password_verify($this->contrasena, $row['contrasena'])) {
                // Generar nuevo token
                $this->token = bin2hex(random_bytes(25));
                $query = "UPDATE " . $this->tabla . " SET token = :token WHERE id = :id";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':token', $this->token);
                $stmt->bindParam(':id', $this->id);

                if ($stmt->execute()) {
                    return true;
                }
            }
        }
        return false;
    }

    // Método para verificar el token (podría usarse para autenticar peticiones API)
    public function verificarToken() {
        $query = "SELECT id FROM " . $this->tabla . " WHERE token = :token LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $this->token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>
