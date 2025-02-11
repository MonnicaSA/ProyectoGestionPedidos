DROP DATABASE IF EXISTS GestionPedidos;
CREATE DATABASE GestionPedidos;

USE GestionPedidos;

CREATE TABLE empleados (
    num_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol ENUM('Administrador','Camarero') DEFAULT 'Camarero',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE productos (      
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio_unitario DECIMAL(8,2)
);

CREATE TABLE pedidos (      
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    num_empleado INT,
    precio_total DECIMAL(8,2),
    estado ENUM('Pendiente', 'En preparación', 'Entregado') DEFAULT 'Pendiente',
    FOREIGN KEY (num_empleado) REFERENCES empleados(num_empleado) ON DELETE CASCADE
);

CREATE TABLE detalle_pedido (
    id_pedido INT,
    id_producto INT, 
    cantidad_producto INT,
    PRIMARY KEY (id_pedido, id_producto),
 /*   constraint detalle_pk primary key (id_pedido,id_producto),*/
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);

/*Crear usuario
CREATE USER usu_gestion IDENTIFIED BY "usu_gestion";
GRANT ALL ON GestionPedidos.* TO usu_gestion;   */

/*INSERT*/
-- Insertar empleados
INSERT INTO empleados (nombre, email, contrasenia, rol) VALUES
('Juan Pérez', 'juan.perez@email.com', 'clave123', 'Administrador'),
('María López', 'maria.lopez@email.com', 'password456', 'Camarero'),
('Carlos Sánchez', 'carlos.sanchez@email.com', 'securepass789', 'Camarero'),
('Ana Torres', 'ana.torres@email.com', 'ana2024', 'Camarero'),
('Luis Gómez', 'luis.gomez@email.com', 'luispass123', 'Administrador');

-- Insertar productos
INSERT INTO productos (nombre, descripcion, precio_unitario) VALUES
('Café Americano', 'Café negro sin azúcar', 2.50),
('Café Espresso', 'Café fuerte y concentrado', 3.00),
('Té Verde', 'Infusión de té verde con antioxidantes', 2.00),
('Croissant', 'Pan dulce con mantequilla', 1.80),
('Sandwich de Pollo', 'Pan con pollo, lechuga y mayonesa', 4.50),
('Jugo de Naranja', 'Jugo natural exprimido', 3.20),
('Tostadas con Mermelada', 'Tostadas con mermelada de fresa', 2.30),
('Capuchino', 'Café con leche espumada y canela', 3.50),
('Brownie de Chocolate', 'Brownie casero con nueces', 2.80),
('Hamburguesa Clásica', 'Pan, carne, lechuga, tomate y queso', 5.50);
    