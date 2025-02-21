/***********REGISTER***************/
const  apiUrl = "/api/";  //Ruta base para los endpoints de la Api
const registerForm = document.getElementById("registerForm");

//manejo de registro
if (registerForm) { // Solo ejecutar si estamos en register.html - Evita error en login 
    registerForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        const nombre = document.getElementById('nombre').value;
        const email = document.getElementById('email').value;
        const rol = document.getElementById('rol').value;
        const contrasenia = document.getElementById('contrasenia').value;

        console.log("Enviando datos de registro:", { nombre, email, rol, contrasenia });

        const response = await fetch(apiUrl + "register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nombre, email, contrasenia, rol })
        });

        const result = await response.json();
        console.log(result);
        alert(result.message || result.error);

        // Si el registro fue exitoso, redirigir a index.html (login)
        if (response.ok && !result.error) {
            window.location.href = "index.html"; // Redirigir al login
        }
    });
}


document.addEventListener("DOMContentLoaded", async () => {    
    /****** LOGIN *********/
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", async function(event) {
            event.preventDefault();

            const email = document.getElementById("email").value;
            const contrasenia = document.getElementById("contrasenia").value;

            const response = await fetch("/api/login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, contrasenia }),
                credentials: "include"
            });

            const data = await response.json();
            if (data.numEmpleado) {
                window.location.href = "lista_pedidos.html";
            } else {
                alert(data.message || "Error en el login");
            }
        });
    }

    /****** LISTAR PEDIDOS Y AÑADIR *********/
    const listaPedidos = document.getElementById("listaPedidos");
    const btnAgregarPedido = document.getElementById("btnAgregarPedido");
    const modalProductos = document.getElementById("modalProductos");
    const listaProductos = document.getElementById("listaProductos");
    const btnEnviarPedido = document.getElementById("btnEnviarPedido");

    if (listaPedidos && btnAgregarPedido && modalProductos && listaProductos && btnEnviarPedido) {
        async function cargarPedidos() {
            const response = await fetch("/api/pedidos.php", { credentials: "include" });
            const pedidos = await response.json();
        
            if (!pedidos || !Array.isArray(pedidos)) {
                console.error("Error: la respuesta del servidor no es un array válido.", pedidos);
                return;
            }
        
            listaPedidos.innerHTML = pedidos.map(pedido => `
                <li>
                    <strong>Pedido ${pedido.id_pedido}</strong> - Estado: ${pedido.estado}
                    <ul>
                        ${(pedido.productos || []).map(prod => `
                            <li>${prod.nombre} - Cantidad: ${prod.cantidad}</li>
                        `).join("")}
                    </ul>
                </li>
            `).join("");
        }
        
        

        async function cargarProductos() {
            const response = await fetch("/api/productos.php", { credentials: "include" });
            const productos = await response.json();
        
            // Agrupar productos por tipo
            const productosPorTipo = {};
            productos.forEach(p => {
                if (!productosPorTipo[p.tipo]) {
                    productosPorTipo[p.tipo] = [];
                }
                productosPorTipo[p.tipo].push(p);
            });
        
            // Renderizar productos agrupados por tipo
            listaProductos.innerHTML = Object.keys(productosPorTipo).map(tipo => `
                <h3>${tipo}</h3>
                <ul>
                    ${productosPorTipo[tipo].map(p => `
                        <li>
                            ${p.nombre} - ${p.precio_unitario}€
                            <input type="number" min="1" value="0" data-id="${p.id_producto}">
                        </li>
                    `).join("")}
                </ul>
            `).join("");
        
            modalProductos.style.display = "block";
        }
        

        btnAgregarPedido.addEventListener("click", cargarProductos);

        btnEnviarPedido.addEventListener("click", async () => {
            const productosSeleccionados = [];
            document.querySelectorAll("#listaProductos input").forEach(input => {
                const cantidad = parseInt(input.value);
                if (cantidad > 0) {
                    productosSeleccionados.push({ id_producto: input.dataset.id, cantidad });
                }
            });

            if (productosSeleccionados.length === 0) {
                alert("Selecciona al menos un producto.");
                return;
            }

            const response = await fetch("/api/pedidos.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "include",
                body: JSON.stringify({ productos: productosSeleccionados })
            });

            const data = await response.json();
            alert(data.message || "Error al crear pedido");
            modalProductos.style.display = "none";
            cargarPedidos();
        });

        cargarPedidos();
    }
});
