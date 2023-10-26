<?php

require 'flight/Flight.php';

// Configuración para la conexión con la base de datos
const DRIVER = 'mysql';
const HOST = 'localhost';
const PORT = 3306;
const DATABASE = 'bd_pasajes';
const USER = 'root';
const PASS = '';
const URL = DRIVER . ':host=' . HOST . ';port=' . PORT . ';dbname=' . DATABASE;

Flight::register('db', 'PDO', array(URL, USER, PASS));

Flight::route('/', function() {
    Flight::json(["mensaje" => "Api de gestión de pasajes"]);
});

Flight::route('GET /pasajes', function () {
    try {
        $sentencia = Flight::db()->prepare("SELECT * FROM pasaje");
        $sentencia->setFetchMode(PDO::FETCH_ASSOC);
        $sentencia->execute();
        $datos = $sentencia->fetchAll();
        Flight::json($datos);
    } catch (PDOException $e) {
        Flight::json(['estado' => 'error', 'mensaje' => $e->getMessage()], 500);
    }
});

Flight::route('GET /pasajes/@id', function ($id) {
    try {
        $sentencia = Flight::db()->prepare("SELECT * FROM pasaje WHERE id = ?");
        $sentencia->bindParam(1,$id);
        $sentencia->setFetchMode(PDO::FETCH_ASSOC);
        $sentencia->execute();
        $datos = $sentencia->fetch();
        if ($datos) {
            Flight::json($datos);
        } else {
            Flight::json(['estado' => 'error', 'mensaje' => 'No se encontró el pasaje con el id proporcionado'], 404);
        }
    } catch (PDOException $e) {
        Flight::json(['estado' => 'error', 'mensaje' => $e->getMessage()], 500);
    }
});

Flight::route('POST /pasajes', function () {
    $request = Flight::request();

    // Validar los datos del pasaje
    $nombre = $request->data->nombre;
    $apellido = $request->data->apellido;
    $documentoIdentidad = $request->data->documentoIdentidad;
    $telefono = $request->data->telefono;
    $origen = $request->data->origen;
    $destino = $request->data->destino;
    $fecha = $request->data->fecha;
    $hora = $request->data->hora;
    $precio = $request->data->precio;

    // Preparar la sentencia SQL
    $sql = "INSERT INTO pasaje (nombre, apellido, documentoIdentidad, telefono, origen, destino, fecha, hora, precio) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        // Ejecutar la sentencia SQL
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->bindParam(1,$nombre);
        $sentencia->bindParam(2,$apellido);
        $sentencia->bindParam(3,$documentoIdentidad);
        $sentencia->bindParam(4,$telefono);
        $sentencia->bindParam(5,$origen);
        $sentencia->bindParam(6,$destino);
        $sentencia->bindParam(7,$fecha);
        $sentencia->bindParam(8,$hora);
        $sentencia->bindParam(9,$precio);
        $sentencia->execute();

        Flight::json(["estado" => "exito", "mensaje" => "Pasaje agregado"], 201);
    } catch (PDOException $e) {
        Flight::json(['estado' => 'error', 'mensaje' => 'Hubo un error al agregar el pasaje: ' . $e->getMessage()], 500);
    }
});

Flight::route('PUT /pasajes/@id', function ($id) {
    $request = Flight::request();

    // Validar los datos del pasaje
    $nombre = $request->data->nombre;
    $apellido = $request->data->apellido;
    $documentoIdentidad = $request->data->documentoIdentidad;
    $telefono = $request->data->telefono;
    $origen = $request->data->origen;
    $destino = $request->data->destino;
    $fecha = $request->data->fecha;
    $hora = $request->data->hora;
    $precio = $request->data->precio;

    // Preparar la sentencia SQL
    $sql = "UPDATE pasaje SET nombre = ?, apellido = ?, documentoIdentidad = ?, telefono = ?, origen = ?, destino = ?, 
    fecha = ?, hora = ?, precio = ? WHERE id = ?";

    try {
        // Ejecutar la sentencia SQL
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->execute([$nombre, $apellido, $documentoIdentidad, $telefono, $origen, $destino, $fecha, $hora, $precio, $id]);

        Flight::json(["estado" => "exito", "mensaje" => "Pasaje modificado"], 200);
    } catch (PDOException $e) {
        Flight::json(['estado' => 'error', 'mensaje' => 'Hubo un error al modificar el pasaje: ' . $e->getMessage()], 500);
    }
});

Flight::route('DELETE /pasajes/@id', function ($id) {
    // Preparar la sentencia SQL
    $sql = "DELETE FROM pasaje WHERE id = ?";

    try {
        // Ejecutar la sentencia SQL
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->bindParam(1,$id);
        $sentencia->execute();

        Flight::json(["estado" => "exito", "mensaje" => "Pasaje borrado"], 200);
    } catch (PDOException $e) {
        Flight::json(['estado' => 'error', 'mensaje' => 'Hubo un error al borrar el pasaje: ' . $e->getMessage()], 500);
    }
});

Flight::start();
