<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// api.php
include_once 'Database.php';
include_once 'Usuario.php';

/*header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");*/

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Obtener la solicitud
$request = json_decode(file_get_contents("php://input"));

// Verificar si la acción está establecida
if (!empty($request->action)) {
    switch ($request->action) {
        case 'register':
            if (!empty($request->correo) && !empty($request->contrasena) && !empty($request->rol)) {
                $usuario->correo = $request->correo;
                $usuario->contrasena = $request->contrasena;
                $usuario->rol = $request->rol;

                if ($usuario->registrar()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Usuario registrado exitosamente."));
                } else {
                    http_response_code(400);
                    echo json_encode(array("message" => "No se pudo registrar al usuario."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Datos incompletos."));
            }
            break;

        case 'login':
            if (!empty($request->correo) && !empty($request->contrasena)) {
                $usuario->correo = $request->correo;
                $usuario->contrasena = $request->contrasena;

                if ($usuario->login()) {
                    http_response_code(200);
                    echo json_encode(array(
                        "message" => "Inicio de sesión exitoso.",
                        "token" => $usuario->token,
                        "rol" => $usuario->rol
                    ));
                } else {
                    http_response_code(401);
                    echo json_encode(array("message" => "Credenciales incorrectas."));
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Datos incompletos."));
            }
            break;

        // Aquí puedes agregar más casos para diferentes acciones de la API

        default:
            http_response_code(400);
            echo json_encode(array("message" => "Acción no válida."));
            break;
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Acción no especificada."));
}
?>
