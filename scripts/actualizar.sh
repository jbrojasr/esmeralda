#!/usr/bin/env bash
# Descarga la última versión desde git y la instala en el servidor,
# conservando jugadores, usuarios y fotos.
#   Uso (dentro de la carpeta del repositorio):  sudo bash scripts/actualizar.sh
set -euo pipefail
# Todo va entre llaves para que bash lo lea completo antes de que git pull cambie este archivo.
{
cd "$(dirname "${BASH_SOURCE[0]}")/.."
[[ $EUID -eq 0 ]] || { echo "Ejecute con sudo: sudo bash scripts/actualizar.sh"; exit 1; }

echo "==> Respaldo previo de la base de datos"
bash scripts/respaldo.sh

echo "==> Descargando cambios (git pull)"
DUENO=$(stat -c '%U' .)
sudo -u "$DUENO" git pull --ff-only

echo "==> Instalando la nueva versión"
bash instalar_vps.sh
exit 0
}
