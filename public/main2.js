
document.addEventListener("DOMContentLoaded", () => {
    if (window.location.pathname.includes("productos.html")) {
        console.log("Página de productos detectada. Cargando Stripe...");

        const stripe = Stripe("pk_test_51QxDBtHWo5rBzQzob6vfuByHi6f2NOwOqnpCw6pLzBCAJENjY1frLgK4cG1N7JSx97WaATPsJdTLKLcrnabl5D1Y00UgMwCQJi"); // Reemplaza con tu clave pública
        const checkoutButton = document.getElementById("checkout-button");

        if (!checkoutButton) {
            console.warn("Botón de pago no encontrado.");
            return;
        }
    
        checkoutButton.addEventListener("click", async () => {
            try {
                const totalPedido = calcularTotalPedido();
                console.log("Total enviado:", totalPedido);
    
                const response = await fetch("/api/checkout.php", { 
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    credentials: "same-origin",
                    body: JSON.stringify({ total: totalPedido }) 
                });
    
                const text = await response.text(); // Leer como texto
                console.log("Respuesta cruda del servidor:", text);
    
                // Intentamos convertir la respuesta en JSON
                let session;
                try {
                    session = JSON.parse(text);
                } catch (jsonError) {
                    console.error("Error al convertir respuesta a JSON:", jsonError);
                    alert("Error en la respuesta del servidor.");
                    return;
                }
    
                if (session && session.id) {
                    console.log("Session ID recibido:", session.id);
                    await stripe.redirectToCheckout({ sessionId: session.id });
                } else {
                    console.error("Error: No se recibió sessionId");
                    alert("Hubo un error al generar la sesión de pago.");
                }
            } catch (error) {
                console.error("Error en el pago:", error);
                alert("Hubo un error al procesar el pago.");
            }
        });
        

        // Función para calcular el precio total del pedido
        function calcularTotalPedido() {
            let total = 0;
            document.querySelectorAll("#contenedorProductos input").forEach(input => {
                const cantidad = parseInt(input.value);
                const precioUnitario = parseFloat(input.dataset.precio);
        
                // Depuración: Verificar valores
                console.log(`Producto ID ${input.dataset.id}: Cantidad = ${cantidad}, Precio = ${precioUnitario}`);
        
                if (!isNaN(cantidad) && !isNaN(precioUnitario) && cantidad > 0) {
                    total += cantidad * precioUnitario;
                }
            });
        
            console.log("Total calculado antes de conversión:", total);
        
            if (isNaN(total) || total <= 0) {
                console.error("Error: Total calculado es inválido:", total);
                alert("Error: No se puede procesar el pago porque el total del pedido es incorrecto.");
                return 0;
            }
        
            return Math.round(total * 100); // 
        }
        
    }
});
