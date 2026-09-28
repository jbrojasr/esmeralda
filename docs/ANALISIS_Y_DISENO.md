# Aplicación Web Administrativa de Registro de Jugadores
## Escuela de Fútbol La Esmeralda — Municipio Girardot, Maracay, estado Aragua, Venezuela

Este documento resume las bases (etapas 1, 2 y 3) sobre las que se construyó el sistema (etapa 4).
Sirve como punto de partida para los capítulos del informe; los datos reales de la escuela
(matrícula, número de entrenadores, problemas observados) deben completarse con el diagnóstico en sitio.

---

## 1. Planteamiento (resumen para el Capítulo I)

**Situación actual (supuesta, a validar con la escuela):** el registro de jugadores se lleva
en planillas de papel, cuadernos u hojas de cálculo sueltas. Esto provoca:

- Datos duplicados o desactualizados de jugadores y representantes.
- Demora para ubicar la ficha de un niño, su categoría o el teléfono del representante.
- Dificultad para generar listados por categoría y constancias de inscripción.
- Riesgo de pérdida de información (deterioro, extravío).
- Ausencia de datos médicos a la mano (tipo de sangre, alergias) durante entrenamientos y partidos.

**Objetivo general:** Desarrollar una aplicación web administrativa para el registro y control
de los jugadores de la Escuela de Fútbol La Esmeralda, ubicada en el municipio Girardot de Maracay.

**Objetivos específicos (sugeridos):**

1. Diagnosticar el proceso actual de registro de jugadores de la escuela.
2. Determinar los requerimientos funcionales y no funcionales del sistema.
3. Diseñar la base de datos y las interfaces de la aplicación.
4. Codificar la aplicación web con PHP y MySQL.
5. Probar el funcionamiento de la aplicación con datos de la escuela.

---

## 2. Requerimientos

### 2.1 Actores (usuarios del sistema)

| Rol | Descripción | Permisos |
|---|---|---|
| **Administrador** | Director/coordinador de la escuela | Todo: jugadores, representantes, categorías, usuarios, bitácora |
| **Secretaria** | Personal administrativo | Registrar y editar jugadores y representantes, reportes |
| **Entrenador** | Técnico de una o varias categorías | Solo consulta de jugadores y reportes |

### 2.2 Requerimientos funcionales

| Código | Requerimiento |
|---|---|
| RF-01 | Iniciar y cerrar sesión con usuario y contraseña. |
| RF-02 | Registrar, consultar, modificar y cambiar el estado (activo/inactivo/retirado) de jugadores. |
| RF-03 | Asignar automáticamente la categoría sugerida según el año de nacimiento del jugador. |
| RF-04 | Cargar la fotografía del jugador. |
| RF-05 | Registrar datos médicos básicos: tipo de sangre, alergias, condición médica, peso, estatura. |
| RF-06 | Registrar, consultar y modificar representantes y vincularlos a uno o varios jugadores. |
| RF-07 | Administrar las categorías (Sub-6, Sub-8…), su rango de edad, horario y entrenador. |
| RF-08 | Buscar jugadores por nombre, apellido o cédula y filtrarlos por categoría y estado. |
| RF-09 | Generar ficha del jugador, constancia de inscripción y listado por categoría (imprimibles). |
| RF-10 | Exportar el listado de jugadores a Excel (CSV). |
| RF-11 | Administrar usuarios del sistema y sus roles (solo administrador). |
| RF-12 | Registrar en una bitácora las acciones de los usuarios. |
| RF-13 | Mostrar un panel con estadísticas: total de jugadores, por categoría, por sexo. |

### 2.3 Requerimientos no funcionales

- **Seguridad:** contraseñas cifradas (bcrypt), consultas preparadas (anti inyección SQL),
  protección CSRF en formularios, control de acceso por rol, validación de archivos subidos.
- **Usabilidad:** interfaz en español, adaptable a teléfonos y computadoras.
- **Disponibilidad sin internet:** no depende de CDNs externos; funciona en red local con XAMPP.
- **Portabilidad:** PHP 8 + MySQL/MariaDB, ejecutable en Windows, Linux o macOS.
- **Mantenibilidad:** código organizado, comentado y sin frameworks pesados.

---

## 3. Casos de uso principales

```
                 ┌──────────────────────────────────────────┐
                 │         Sistema La Esmeralda             │
 Administrador ──┼─► Gestionar usuarios                     │
       │         ├─► Gestionar categorías                   │
       │         ├─► Consultar bitácora                     │
       ▼         │                                          │
  Secretaria ────┼─► Registrar / editar jugador ──«include»─► Registrar representante
       │         ├─► Cambiar estado del jugador             │
       ▼         │                                          │
  Entrenador ────┼─► Consultar jugadores / ficha            │
                 ├─► Generar reportes (listado, constancia) │
                 └─► Iniciar / cerrar sesión                │
                 └──────────────────────────────────────────┘
```
(Herencia de roles: el Administrador puede todo lo de la Secretaria, y ésta todo lo del Entrenador.)

---

## 4. Modelo de datos (entidad-relación)

```
 usuarios (1) ──────< (N) bitacora
    │
    └──(1)──< (N) jugadores >(N)──(1) categorias
                     │
                     └ (N) >──(1) representantes
```

| Tabla | Campos principales |
|---|---|
| **usuarios** | id, nombre, usuario, clave (hash), rol, activo, creado_en |
| **categorias** | id, nombre, edad_min, edad_max, horario, entrenador, descripcion |
| **representantes** | id, cedula, nombres, apellidos, parentesco, telefono, telefono_alt, correo, direccion, ocupacion |
| **jugadores** | id, cedula, nombres, apellidos, fecha_nacimiento, sexo, lugar_nacimiento, direccion, telefono, institucion_educativa, grado, posicion, pie_dominante, numero_camiseta, talla_camisa, tipo_sangre, alergias, condicion_medica, peso, estatura, foto, categoria_id, representante_id, fecha_inscripcion, estado, observaciones, creado_por |
| **bitacora** | id, usuario_id, accion, detalle, fecha |

**Regla de categoría:** edad deportiva = año en curso − año de nacimiento (criterio habitual en
fútbol menor). El sistema sugiere la categoría cuyo rango incluye esa edad; el usuario puede cambiarla.

---

## 5. Metodología y tecnologías

- **Metodología sugerida:** Programación Extrema (XP) o RUP/cascada, según exija la cátedra.
  El desarrollo se organizó por módulos (iteraciones): autenticación → jugadores → representantes
  → categorías → reportes → usuarios/bitácora.
- **Lenguaje:** PHP 8 (sin framework, patrón de páginas + funciones reutilizables).
- **Base de datos:** MySQL 5.7+ / MariaDB 10+ (motor InnoDB, utf8mb4).
- **Presentación:** HTML5, CSS3 propio, JavaScript mínimo.
- **Servidor local:** XAMPP (Apache + MySQL).
