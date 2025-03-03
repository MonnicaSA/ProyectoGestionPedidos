/*********** REGISTER ***************/
const apiUrl = "/api/"; // Ruta base para los endpoints de la API
const registerForm = document.getElementById("registerForm");

// Manejo de registro
if (registerForm) {
  registerForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const nombre = document.getElementById("nombre").value;
    const email = document.getElementById("email").value;
    const rol = document.getElementById("rol").value;
    const contrasenia = document.getElementById("contrasenia").value;

    console.log("Enviando datos de registro:", {
      nombre,
      email,
      rol,
      contrasenia,
    });

    try {
      const response = await fetch(apiUrl + "register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombre, email, contrasenia, rol }),
      });

      const result = await response.json();
      console.log(result);
      alert(result.message || result.error);

      // Si el registro fue exitoso, redirigir a index.html (login)
      if (response.ok && !result.error) {
        window.location.href = "index.html"; // Redirigir al login
      }
    } catch (error) {
      console.error("Error en la solicitud:", error);
      alert("Hubo un error al registrar el usuario.");
    }
  });
}

/*********** LOGIN ***************/
const loginForm = document.getElementById("loginForm");

if (loginForm) {
  loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const contrasenia = document.getElementById("contrasenia").value;

    try {
      const response = await fetch("/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, contrasenia }),
        credentials: "include",
      });

      const data = await response.json();
      if (data.numEmpleado) {
        window.location.href = "lista_pedidos.html";
      } else {
        alert(data.message || "Error en el login");
      }
    } catch (error) {
      console.error("Error en la solicitud:", error);
      alert("Hubo un error al intentar iniciar sesión.");
    }
  });
}

/*********** LISTAR PEDIDOS Y AÑADIR ***************/
document.addEventListener("DOMContentLoaded", async () => {
  const apiUrlPedidos = "/api/pedidos1.php"; // Ruta base para los endpoints de la API de pedidos
  const listaPedidos = document
    .getElementById("tablaPedidos")
    ?.querySelector("tbody");
  const modalActualizarEstado = document.getElementById(
    "modalActualizarEstado"
  );
  const btnActualizarPedido = document.getElementById("btnActualizarPedido");
  const btnCancelarActualizar = document.getElementById(
    "btnCancelarActualizar"
  );
  const estadoPedido = document.getElementById("estadoPedido");

  let pedidoSeleccionado = null; // Para almacenar el pedido que se está actualizando

  /***********productos **************/
  // Verificar si estamos en la página de productos
  if (window.location.pathname.includes("productos.html")) {
    const contenedorProductos = document.getElementById("contenedorProductos");
    const btnConfirmarPedido = document.getElementById("btnConfirmarPedido");

    // Función para cargar los productos
    async function cargarProductos() {
      try {
        const response = await fetch("/api/productos.php", {
          credentials: "include",
        });
        const productos = await response.json();

        if (productos && productos.length > 0) {
          // Agrupar productos por tipo
          const productosPorTipo = productos.reduce((acc, producto) => {
            if (!acc[producto.tipo]) {
              acc[producto.tipo] = [];
            }
            acc[producto.tipo].push(producto);
            return acc;
          }, {});

          // Mostrar los productos agrupados por tipo
          contenedorProductos.innerHTML = Object.keys(productosPorTipo)
            .map(
              (tipo) => `
                        <h3>${tipo}</h3>
                        <ul>
                            ${productosPorTipo[tipo]
                              .map(
                                (producto) => `
                                <li>
                                    ${producto.nombre} - ${producto.precio_unitario}€
                                    <input type="number" min="0" value="0" data-id="${producto.id_producto}"
                                    data-precio="${producto.precio_unitario}">
                                </li>
                            `
                              )
                              .join("")}
                        </ul>
                    `
            )
            .join("");
        } else {
          contenedorProductos.innerHTML =
            "<p>No hay productos disponibles.</p>";
        }
      } catch (error) {
        console.error("Error en la solicitud:", error);
        contenedorProductos.innerHTML = "<p>Error al cargar los productos.</p>";
      }
    }

    // Función para confirmar el pedido
    btnConfirmarPedido.addEventListener("click", async () => {
      const productosSeleccionados = [];
      document
        .querySelectorAll("#contenedorProductos input")
        .forEach((input) => {
          const cantidad = parseInt(input.value);
          if (cantidad > 0) {
            productosSeleccionados.push({
              id_producto: input.dataset.id,
              cantidad,
            });
          }
        });

      if (productosSeleccionados.length === 0) {
        alert("Selecciona al menos un producto.");
        return;
      }

      try {
        const response = await fetch("/api/pedidos1.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ productos: productosSeleccionados }),
        });

        const data = await response.json();
        alert(data.message || "Error al crear pedido");
        window.location.href = "lista_pedidos.html"; // Redirigir a la lista de pedidos
      } catch (error) {
        console.error("Error al crear el pedido:", error);
        alert("Hubo un error al crear el pedido.");
      }
    });

    // Cargar los productos al iniciar la página
    cargarProductos();
  }

  // Botón de cerrar sesión
  const btnCerrarSesion = document.getElementById("btnCerrarSesion");

  if (btnCerrarSesion) {
    btnCerrarSesion.addEventListener("click", async () => {
      try {
        // Hacer una solicitud al servidor para cerrar la sesión
        const response = await fetch("/api/logout.php", {
          method: "GET",
          credentials: "include", // Incluir cookies en la solicitud
        });

        const data = await response.json();

        if (data.message === "Sesión cerrada") {
          alert("Sesión cerrada correctamente.");
          window.location.href = "index.html"; // Redirigir al index
        } else {
          alert("Error al cerrar la sesión.");
        }
      } catch (error) {
        console.error("Error al cerrar la sesión:", error);
        alert("Hubo un error al cerrar la sesión.");
      }
    });
  }

  // Cargar pedidos al iniciar la página
  async function cargarPedidos() {
    try {
      const response = await fetch(apiUrlPedidos, {
        method: "GET",
        credentials: "include",
      });

      const data = await response.json();

      if (!data.pedidos || !Array.isArray(data.pedidos)) {
        throw new Error("La respuesta del servidor no es un array válido.");
      }

      const listaPedidos = document
        .getElementById("tablaPedidos")
        ?.querySelector("tbody");

      if (!listaPedidos) {
        console.warn("No se encontró la tabla de pedidos en el DOM.");
        return; // Evita errores si la tabla no existe
      }

      listaPedidos.innerHTML = ""; // Limpiar la tabla antes de cargar nuevos pedidos

      if (data.pedidos.length === 0) {
        listaPedidos.innerHTML =
          "<tr><td colspan='6'>No hay pedidos disponibles.</td></tr>";
        return;
      }

      data.pedidos.forEach((pedido) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
                <td>${pedido.id_pedido}</td>
                <td>${pedido.empleado}</td>
                <td>${pedido.fecha}</td>
                <td>${pedido.estado}</td>
                <td>${Number(pedido.precio_total).toFixed(2)}€</td>
                <td>
                    <button onclick="verDetalle(${
                      pedido.id_pedido
                    })">Detalle</button>
                    <button onclick="eliminarPedido(${
                      pedido.id_pedido
                    })">Eliminar</button>
                    <button onclick="abrirModalActualizar(${
                      pedido.id_pedido
                    })">Actualizar</button>
                </td>
            `;
        listaPedidos.appendChild(fila);
      });
    } catch (error) {
      console.error("Error al cargar los pedidos:", error);
    }
  }
  // Función para ver detalles del pedido
  window.verDetalle = function (idPedido) {
    // window.location.href = `detalle_pedido_${idPedido}.html`; // Redirigir a la página de detalle
    window.location.href = `detalle_pedido.html?id=${idPedido}`; //pasa el pedido en la URL
  };

  /********CARGAR LOS DETALLES DEL PEDIDO********** */
  // Verificar si estamos en la página de detalle_pedido.html
  if (window.location.pathname.includes("detalle_pedido.html")) {
    const tablaDetalle = document.getElementById("tablaDetalle");

    if (!tablaDetalle) {
      console.error("No se encontró la tabla con ID 'tablaDetalle'.");
      return;
    }

    const tbody = tablaDetalle.querySelector("tbody");

    if (!tbody) {
      console.error("No se encontró el elemento <tbody> en la tabla.");
      return;
    }

    // Obtener el ID del pedido desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idPedido = urlParams.get("id");

    if (!idPedido) {
      console.error("No se encontró el ID del pedido en la URL.");
      return;
    }

    try {
      // Hacer una solicitud GET a la API para obtener los detalles del pedido
      const response = await fetch(`/api/pedidos1.php?id=${idPedido}`, {
        method: "GET",
        credentials: "include",
      });

      const data = await response.json();

      if (!data.pedidos || !Array.isArray(data.pedidos)) {
        throw new Error("La respuesta del servidor no es un array válido.");
      }

      // Limpiar la tabla antes de cargar nuevos datos
      tbody.innerHTML = "";

      // Recorrer los productos del pedido y agregarlos a la tabla
      data.pedidos.forEach((pedido) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
        <td>${pedido.producto}</td>
        <td>${pedido.cantidad_producto}</td>
        <td>${Number(pedido.precio_unitario).toFixed(2)}€</td>
        <td>${Number(pedido.cantidad_producto * pedido.precio_unitario).toFixed(
          2
        )}€</td>
      `;
        tbody.appendChild(fila);
      });
    } catch (error) {
      console.error("Error al cargar los detalles del pedido:", error);
    }
  }

  /************Función para eliminar un pedido************/

  window.eliminarPedido = async function (idPedido) {
    const confirmacion = confirm(
      `¿Estás seguro de eliminar el pedido #${idPedido}?`
    );
    if (confirmacion) {
      try {
        const response = await fetch(apiUrlPedidos, {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({ id_pedido: idPedido }),
        });

        const data = await response.json();
        alert(data.message || "Error al eliminar el pedido");
        cargarPedidos(); // Recargar la lista de pedidos
      } catch (error) {
        console.error("Error al eliminar el pedido:", error);
        alert("Hubo un error al eliminar el pedido.");
      }
    }
  };

  // Función para abrir el modal de actualizar estado
  window.abrirModalActualizar = function (idPedido) {
    pedidoSeleccionado = idPedido;
    modalActualizarEstado.style.display = "block";
  };

  /*********Función para actualizar el estado del pedido********/
  // Verificar si existen los elementos antes de asignar eventos
  if (btnActualizarPedido) {
    btnActualizarPedido.addEventListener("click", async () => {
      if (!pedidoSeleccionado) return;

      const nuevoEstado = estadoPedido.value;
      try {
        const response = await fetch(apiUrlPedidos, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          credentials: "include",
          body: JSON.stringify({
            id_pedido: pedidoSeleccionado,
            estado: nuevoEstado,
          }),
        });

        const data = await response.json();
        alert(data.message || "Error al actualizar el pedido");
        modalActualizarEstado.style.display = "none";
        cargarPedidos(); // Recargar la lista de pedidos
      } catch (error) {
        console.error("Error al actualizar el pedido:", error);
        alert("Hubo un error al actualizar el pedido.");
      }
    });
  } else {
    console.warn("btnActualizarPedido no encontrado en el DOM.");
  }

  // Verificar si el botón de cancelar existe antes de agregar evento
  if (btnCancelarActualizar) {
    btnCancelarActualizar.addEventListener("click", () => {
      modalActualizarEstado.style.display = "none";
    });
  } else {
    console.warn("btnCancelarActualizar no encontrado en el DOM.");
  }
  cargarPedidos();
});
