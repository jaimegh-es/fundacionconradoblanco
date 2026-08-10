#!/bin/bash
# Script para empaquetar el tema fundacion-conrado-blanco en formato .zip listo para WordPress.

THEME_DIR="/home/jaime/Documentos/fundacionelevate/wp-content/themes/fundacion-conrado-blanco"
OUTPUT_DIR="/home/jaime/Documentos/fundacionelevate"
ZIP_NAME="fundacion-conrado-blanco.zip"

echo "=================================================="
echo "Empaquetando el tema Fundacion Conrado Blanco..."
echo "=================================================="

# Comprobar si existe la carpeta del tema
if [ ! -d "$THEME_DIR" ]; then
    echo "Error: La carpeta del tema no existe en $THEME_DIR"
    exit 1
fi

# Eliminar archivo zip anterior si existe
if [ -f "$OUTPUT_DIR/$ZIP_NAME" ]; then
    echo "Eliminando archivo zip anterior..."
    rm "$OUTPUT_DIR/$ZIP_NAME"
fi

# Crear el paquete ZIP excluyendo carpetas de control de versiones y metadatos de desarrollo
cd "$THEME_DIR"
zip -r "$OUTPUT_DIR/$ZIP_NAME" . -x "*.git*" "*node_modules*" "*.DS_Store*" "*import-books.php*" "*zip-theme.sh*"

if [ $? -eq 0 ]; then
    echo "=================================================="
    echo "¡Éxito! Archivo creado: $OUTPUT_DIR/$ZIP_NAME"
    echo "=================================================="
else
    echo "Error: Ocurrió un problema al crear el archivo zip."
    exit 1
fi
