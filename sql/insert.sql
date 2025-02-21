-- Platos principales
INSERT INTO productos (nombre, descripcion, tipo, precio_unitario) VALUES
('Lasaña de Carne', 'Lasaña casera con carne molida y salsa de tomate', 'Plato Principal', 8.50),
('Pollo a la Parrilla', 'Pechuga de pollo asada con especias', 'Plato Principal', 7.80),
('Paella Valenciana', 'Arroz con mariscos, pollo y verduras', 'Plato Principal', 12.00),
('Filete de Salmón', 'Filete de salmón a la plancha con limón', 'Plato Principal', 10.50),
('Risotto de Champiñones', 'Arroz cremoso con champiñones y queso parmesano', 'Plato Principal', 9.00),
('Tacos de Carne', 'Tortillas de maíz rellenas de carne de res y salsa', 'Plato Principal', 6.50),
('Pizza Margarita', 'Pizza con salsa de tomate, mozzarella y albahaca', 'Plato Principal', 7.00),
('Curry de Pollo', 'Pollo en salsa de curry con arroz basmati', 'Plato Principal', 8.80),
('Spaghetti Carbonara', 'Pasta con salsa de huevo, queso y panceta', 'Plato Principal', 7.50),
('Burrito de Cerdo', 'Tortilla de trigo rellena de cerdo, frijoles y arroz', 'Plato Principal', 6.80);

-- Entrantes
INSERT INTO productos (nombre, descripcion, tipo, precio_unitario) VALUES
('Ensalada César', 'Lechuga, pollo, croutones y aderezo César', 'Entrante', 5.00),
('Bruschetta', 'Pan tostado con tomate, ajo y albahaca', 'Entrante', 4.20),
('Sopa de Tomate', 'Sopa cremosa de tomate con albahaca', 'Entrante', 3.80),
('Patatas Bravas', 'Patatas fritas con salsa brava y alioli', 'Entrante', 4.50),
('Croquetas de Jamón', 'Croquetas caseras rellenas de jamón serrano', 'Entrante', 5.20),
('Empanadillas de Carne', 'Empanadillas rellenas de carne picada', 'Entrante', 4.80),
('Calamares a la Romana', 'Calamares rebozados y fritos', 'Entrante', 6.00),
('Nachos con Queso', 'Nachos cubiertos con queso fundido y jalapeños', 'Entrante', 5.50),
('Hummus con Pan de Pita', 'Puré de garbanzos con aceite de oliva y pan de pita', 'Entrante', 4.00),
('Aros de Cebolla', 'Aros de cebolla rebozados y fritos', 'Entrante', 4.20);

-- Postres
INSERT INTO productos (nombre, descripcion, tipo, precio_unitario) VALUES
('Tarta de Queso', 'Tarta cremosa de queso con base de galleta', 'Postre', 4.50),
('Flan de Huevo', 'Postre tradicional de huevo y caramelo', 'Postre', 3.80),
('Helado de Vainilla', 'Helado cremoso de vainilla', 'Postre', 3.00),
('Brownie con Helado', 'Brownie de chocolate con helado de vainilla', 'Postre', 5.00),
('Mousse de Chocolate', 'Mousse ligero y esponjoso de chocolate', 'Postre', 4.20),
('Crema Catalana', 'Crema quemada con azúcar caramelizado', 'Postre', 4.00),
('Tiramisú', 'Postre italiano con café y mascarpone', 'Postre', 4.80),
('Pastel de Zanahoria', 'Pastel esponjoso con zanahoria y nueces', 'Postre', 4.50),
('Gelatina de Frutas', 'Gelatina con trozos de frutas naturales', 'Postre', 2.80),
('Profiteroles', 'Bolitas de hojaldre rellenas de crema y chocolate', 'Postre', 4.20);

-- Bebidas
INSERT INTO productos (nombre, descripcion, tipo, precio_unitario) VALUES
('Agua Mineral', 'Agua natural sin gas', 'Bebida', 1.50),
('Refresco de Cola', 'Refresco de cola con hielo', 'Bebida', 2.00),
('Zumo de Manzana', 'Zumo natural de manzana', 'Bebida', 2.50),
('Cerveza Artesanal', 'Cerveza rubia artesanal', 'Bebida', 3.50),
('Vino Tinto', 'Vino tinto de la casa', 'Bebida', 4.00),
('Café con Leche', 'Café mezclado con leche caliente', 'Bebida', 2.80),
('Té de Menta', 'Infusión de menta fresca', 'Bebida', 2.20),
('Batido de Fresa', 'Batido cremoso de fresa', 'Bebida', 3.20),
('Smoothie de Mango', 'Smoothie refrescante de mango', 'Bebida', 3.50),
('Cóctel Sin Alcohol', 'Cóctel de frutas sin alcohol', 'Bebida', 4.00);

-- Menú Infantil
INSERT INTO productos (nombre, descripcion, tipo, precio_unitario) VALUES
('Menú Infantil - Nuggets de Pollo', 'Nuggets de pollo con patatas fritas y salsa', 'Menú Infantil', 5.50),
('Menú Infantil - Hamburguesa', 'Hamburguesa pequeña con queso y patatas fritas', 'Menú Infantil', 6.00),
('Menú Infantil - Pasta con Queso', 'Pasta con salsa de queso y trozos de jamón', 'Menú Infantil', 5.80),
('Menú Infantil - Pizza Pequeña', 'Pizza pequeña con jamón y queso', 'Menú Infantil', 5.00),
('Menú Infantil - Perrito Caliente', 'Pan con salchicha y patatas fritas', 'Menú Infantil', 4.80);