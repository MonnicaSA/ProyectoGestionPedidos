<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../stripe/init.php'; // Ruta correcta al SDK de Stripe
\Stripe\Stripe::setApiKey('sk_test_51QxDBtHWo5rBzQzok02HcekitjtZshSlte0axuxAQpyzSmJGN2lF7CV0BrAWEsC4yi9fMiP9v143NLe7BvuFkSD60015L3obk6'); // Reemplaza con clave secreta

header('Content-Type: application/json');

// Leer y decodificar el JSON recibido
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar si la decodificación fue exitosa
if (!$data) {
    file_put_contents("/log_checkout.txt", "Error: No se recibió JSON válido. Entrada: $input" . PHP_EOL, FILE_APPEND);
    echo json_encode(["error" => "No se recibieron datos válidos."]);
    http_response_code(400);
    exit;
}

$total = isset($data['total']) ? intval($data['total']) : 0;

// Registrar el total recibido para depuración
file_put_contents("log_checkout.txt", "Total recibido en PHP: $total" . PHP_EOL, FILE_APPEND);

if ($total <= 0) {
    echo json_encode(["error" => "El precio total del pedido no es válido."]);
    http_response_code(400);
    exit;
}

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => ['name' => 'Pedido en tienda'],
                'unit_amount' => $total, // Usamos el precio total recibido
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:8000/public/success.html',
        'cancel_url' => 'http://localhost:8000/public/productos.html',
    ]);

    file_put_contents("log_checkout.txt", "Session ID: " . $checkout_session->id . PHP_EOL, FILE_APPEND);
    
    echo json_encode(['id' => $checkout_session->id]);
} catch (Exception $e) {
    file_put_contents("/log_checkout.txt", "Stripe Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
