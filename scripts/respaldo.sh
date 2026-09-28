#!/usr/bin/env bash
# Respaldo inmediato de la base de datos y las fotos.
#   Uso:  sudo bash scripts/respaldo.sh
# Los respaldos quedan en /var/backups/esmeralda (también hay uno automático cada día a las 2:30 a.m.).
set -euo pipefail
[[ $EUID -eq 0 ]] || { echo "Ejecute con sudo: sudo bash scripts/respaldo.sh"; exit 1; }
DIR=/var/backups/esmeralda
FECHA=$(date +%Y%m%d_%H%M%S)
mkdir -p "$DIR"; chmod 700 "$DIR"
mysqldump --single-transaction --default-character-set=utf8mb4 esmeralda | gzip > "$DIR/esmeralda_$FECHA.sql.gz"
tar -czf "$DIR/fotos_$FECHA.tar.gz" -C /var/www/esmeralda uploads/fotos
echo "Respaldo creado:"
ls -lh "$DIR"/*_"$FECHA".*
