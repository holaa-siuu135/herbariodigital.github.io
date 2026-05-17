<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit(json_encode(['error' => 'Método no permitido']));
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
if (empty($nombre) || empty($email) || empty($mensaje)) exit(json_encode(['error' => 'Todos los campos son obligatorios']));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) exit(json_encode(['error' => 'Email inválido']));

$stmt = $conn->prepare("INSERT INTO contactos (nombre, email, mensaje) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre, $email, $mensaje);
if ($stmt->execute()) echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente']);
else echo json_encode(['error' => 'Error al guardar']);
?>