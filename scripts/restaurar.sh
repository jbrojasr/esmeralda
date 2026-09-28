#!/usr/bin/env bash
# Restaura la base de datos desde un respaldo .sql.gz (¡reemplaza los datos actuales!).
#   Uso:  sudo bash scripts/restaurar.sh /var/backups/esmeralda/esmeralda_AAAAMMDD.sql.gz
set -euo pipefail
[[ $EUID -eq 0 ]] || { echo "Ejecute con sudo."; exit 1; }
ARCHIVO="${1:-}"
[[ -f "$ARCHIVO" ]] || { echo "Indique el archivo. Disponibles:"; ls -1t /var/backups/esmeralda/*.sql.gz 2>/dev/null | head; exit 1; }
read -r -p "Esto REEMPLAZA los datos actuales con $ARCHIVO. Escriba SI para continuar: " OK
[[ "$OK" == "SI" ]] || { echo "Cancelado."; exit 1; }
gunzip -c "$ARCHIVO" | mysql --default-character-set=utf8mb4 esmeralda
echo "Base de datos restaurada."
