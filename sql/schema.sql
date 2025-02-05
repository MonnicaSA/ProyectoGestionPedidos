/* 
RESTRICCIÓN:
En este diseño de base de datos tenemos una restricción. Sólo se va a poder realizar pedidos 
de un mismo producto, no importa la cantidad, pero sólo de un producto. Entonces la relación 
entre productos y pedidos es de 1:1 . 
*/

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
    precio DECIMAL(8,2)
);

CREATE TABLE pedidos (      
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    precio_total DECIMAL(8,2),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cantidad INT,
    num_empleado INT,
    id_producto INT,
    status ENUM('Pendiente', 'En preparación', 'Entregado') DEFAULT 'Pendiente',
    FOREIGN KEY (num_empleado) REFERENCES empleados(num_empleado) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);


/*Crear usuario
CREATE USER usu_gestion IDENTIFIED BY "usu_gestion";
GRANT ALL ON GestionPedidos.* TO usu_gestion;   */