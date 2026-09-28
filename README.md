# Sistema de Registro de Jugadores — Escuela de Fútbol La Esmeralda

Aplicación web administrativa (PHP 8 + MySQL/MariaDB) para el registro y control de jugadores de la
Escuela de Fútbol La Esmeralda, municipio Girardot, Maracay (Venezuela).

---

## 1. Instalación en un VPS Ubuntu (recomendado)

Requisitos: VPS con **Ubuntu 22.04 o 24.04**, acceso por SSH como `root` o con `sudo`, y el
**puerto 80** abierto (revise también el firewall del panel de su proveedor).

```bash
sudo apt update && sudo apt install -y git
git clone https://github.com/SU_USUARIO/esmeralda.git
cd esmeralda
sudo bash instalar_vps.sh
```

Al terminar, la pantalla muestra la dirección (`http://IP-del-servidor/`) y la **contraseña inicial
de `admin`** (también queda en `/root/esmeralda_credenciales.txt`). Cámbiela al primer ingreso.

El instalador deja configurado:

- Apache + PHP + MariaDB, con la aplicación en `/var/www/esmeralda`.
- Base de datos `esmeralda` con un usuario propio y clave aleatoria (sólo permisos de lectura/escritura de datos).
- Bloqueo del acceso web a `config/`, `includes/`, `database/`, `docs/` y a scripts dentro de `uploads/`.
- Firewall (ufw) con SSH y HTTP abiertos.
- Respaldo automático diario (2:30 a.m.) de la base de datos y las fotos en `/var/backups/esmeralda`, guardando 14 días.

### Si el repositorio es privado

GitHub pide usuario y un **token** (no la contraseña). Créelo en GitHub → *Settings → Developer settings →
Personal access tokens → Fine-grained*, con permiso de sólo lectura (*Contents: Read*) sobre este repositorio,
y úselo como contraseña al hacer `git clone`.

### Mantenimiento en el servidor

| Tarea | Comando (dentro de la carpeta `esmeralda`) |
|---|---|
| Actualizar a la última versión del repositorio | `sudo bash scripts/actualizar.sh` |
| Respaldo inmediato | `sudo bash scripts/respaldo.sh` |
| Restaurar un respaldo | `sudo bash scripts/restaurar.sh /var/backups/esmeralda/esmeralda_AAAAMMDD.sql.gz` |
| Nueva clave para `admin` | `sudo bash instalar_vps.sh --reset-admin` |
| Borrar todo y empezar de cero (¡pierde los datos!) | `sudo bash instalar_vps.sh --reinstalar-bd` |
| Ver errores de la aplicación | `sudo tail -f /var/log/apache2/esmeralda_error.log` |

La actualización hace un respaldo antes, descarga los cambios con `git pull` y reinstala los archivos
**sin tocar** los jugadores, usuarios ni fotos.

---

## 2. Instalación local con XAMPP (para desarrollo o pruebas)

1. Instale **XAMPP** (PHP 8.0 o superior) y encienda **Apache** y **MySQL**.
2. Copie la carpeta del proyecto dentro de `htdocs` con el nombre `esmeralda`.
3. En **phpMyAdmin** → **Importar** → `database/esmeralda.sql`.
   (Opcional: importe también `database/datos_prueba.sql` para tener jugadores de ejemplo).
4. Si su MySQL tiene clave, ajústela en `config/config.php`.
5. Entre a **http://localhost/esmeralda** con usuario `admin` y clave `Esmeralda2026`.

---

## Módulos

| Módulo | Qué hace | Roles |
|---|---|---|
| Panel | Totales, jugadores por categoría, últimos inscritos | Todos |
| Jugadores | Registrar, editar, buscar, filtrar, ficha, foto, estado, exportar a Excel | Consulta: todos · Editar: secretaria/admin · Eliminar: admin |
| Representantes | Registro automático al inscribir; autocompletado por cédula; hermanos | Consulta: todos · Editar: secretaria/admin |
| Categorías | Sub-6 a Sub-18, rango de edad, horario, entrenador; recálculo por temporada | Consulta: todos · Editar: admin |
| Reportes | Listado por categoría, planilla de asistencia, datos médicos, estadística, constancia de inscripción | Todos |
| Usuarios | Crear usuarios y asignar rol (administrador, secretaria, entrenador) | Admin |
| Bitácora | Registro de cada acción (quién, qué, cuándo) | Admin |

## Estructura del repositorio

```
esmeralda/
├── instalar_vps.sh          Instalador/actualizador para Ubuntu
├── scripts/                 actualizar.sh · respaldo.sh · restaurar.sh
├── config/config.php        Conexión y constantes (en el VPS el instalador pone la clave real)
├── includes/                init.php (sesión, PDO, seguridad), header.php, footer.php
├── assets/                  estilos.css, app.js
├── uploads/fotos/           Fotos de los jugadores (no se versionan)
├── database/                esmeralda.sql (estructura + datos iniciales), datos_prueba.sql
├── docs/                    ANALISIS_Y_DISENO.md (requerimientos, casos de uso, modelo de datos)
├── index.php                Panel
├── login.php · logout.php · perfil.php
├── jugadores.php · jugador_form.php · jugador_ver.php · constancia.php
├── representantes.php · representante_form.php · rep_buscar.php
└── categorias.php · reportes.php · usuarios.php · bitacora.php
```

## Seguridad implementada

- Contraseñas cifradas con `password_hash` (bcrypt).
- Consultas preparadas PDO (protección contra inyección SQL).
- Token CSRF en todos los formularios y escape de salida HTML (XSS).
- Control de acceso por rol en cada página y acción.
- Fotos validadas por tipo real (JPG/PNG/WEBP) y tamaño (2 MB); la carpeta de fotos no ejecuta scripts.
- Límite de intentos de inicio de sesión y bitácora de actividades.
- Las fotos y credenciales **nunca** se suben al repositorio (`.gitignore`).

## Regla de categorías

Edad deportiva = año en curso − año de nacimiento. El sistema sugiere la categoría al registrar;
se puede cambiar a mano. Al inicio de cada año: **Categorías → Recalcular categorías**.
