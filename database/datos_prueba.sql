-- Datos de prueba (opcional). Importar DESPUÉS de esmeralda.sql
SET NAMES utf8mb4;
USE esmeralda;
INSERT INTO representantes (cedula, nombres, apellidos, parentesco, telefono, correo, direccion, ocupacion) VALUES
('V-12345678','María José','Pérez Gómez','Madre','0414-4567890','mariajose@correo.com','Urb. La Esmeralda, calle 3, casa 12, Maracay','Docente'),
('V-11222333','Carlos Alberto','Rodríguez','Padre','0424-3456789',NULL,'Barrio San José, Maracay','Comerciante'),
('V-14555666','Yelitza','Hernández','Madre','0412-1112233',NULL,'Urb. Base Aragua, Maracay','Enfermera');

INSERT INTO jugadores (cedula,nombres,apellidos,fecha_nacimiento,sexo,lugar_nacimiento,direccion,institucion_educativa,grado,posicion,pie_dominante,numero_camiseta,talla_camisa,tipo_sangre,alergias,peso,estatura,categoria_id,representante_id,fecha_inscripcion,estado,creado_por) VALUES
(NULL,'Luis Ángel','Pérez Pérez', CONCAT(YEAR(CURDATE())-9,'-03-14'),'M','Maracay','Urb. La Esmeralda, calle 3, casa 12','U.E. Agustín Codazzi','4to grado','Delantero','Derecho',9,'10','O+',NULL,30.5,1.32,(SELECT id FROM categorias WHERE nombre='Sub-10'),1,CURDATE(),'activo',1),
(NULL,'Sofía Valentina','Pérez Pérez', CONCAT(YEAR(CURDATE())-7,'-08-02'),'F','Maracay','Urb. La Esmeralda, calle 3, casa 12','U.E. Agustín Codazzi','2do grado','Mediocampista','Izquierdo',8,'8','O+','Penicilina',24.0,1.20,(SELECT id FROM categorias WHERE nombre='Sub-8'),1,CURDATE(),'activo',1),
('V-32111222','Andrés Eduardo','Rodríguez Silva', CONCAT(YEAR(CURDATE())-13,'-11-20'),'M','Turmero','Barrio San José','Liceo Agustín Codazzi','2do año','Portero','Derecho',1,'M','A+',NULL,48.0,1.58,(SELECT id FROM categorias WHERE nombre='Sub-14'),2,CURDATE(),'activo',1),
(NULL,'Diego José','Hernández', CONCAT(YEAR(CURDATE())-11,'-05-09'),'M','Maracay','Urb. Base Aragua','U.E. Base Aragua','6to grado','Defensa','Ambos',4,'12','B+','Polvo (rinitis)',38.0,1.45,(SELECT id FROM categorias WHERE nombre='Sub-12'),3,CURDATE(),'activo',1);
