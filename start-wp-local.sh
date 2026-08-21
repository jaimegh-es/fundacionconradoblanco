#!/bin/bash
set -e

# ==============================================================================
# Script de Despliegue Local WordPress - Fundación Conrado Blanco
# Usuario Admin WP: fcb
# Contraseña Admin WP: fcb
# Puerto Web: http://localhost:8080
# Puerto phpMyAdmin: http://localhost:8081
# ==============================================================================

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

echo "=== 1. Limpiando contenedores previos si existen ==="
docker rm -f fcb-wp fcb-db fcb-pma fcb-cli fcb-dev-wp-1 fcb-dev-db-1 fcb-dev-pma-1 fcb-dev-wpcli-1 2>/dev/null || true
docker network rm fcb-network 2>/dev/null || true

echo "=== 2. Creando red y volúmenes Docker ==="
docker network create fcb-network || true
docker volume create fcb_wp_data || true
docker volume create fcb_wp_uploads || true
docker volume create fcb_db_data || true

echo "=== 3. Levantando Base de Datos MariaDB 10.11 ==="
docker run -d \
  --name fcb-db \
  --network fcb-network \
  --restart unless-stopped \
  -e MARIADB_DATABASE=wordpress \
  -e MARIADB_USER=wordpress \
  -e MARIADB_PASSWORD=wordpress \
  -e MARIADB_ROOT_PASSWORD=rootpassword \
  -v fcb_db_data:/var/lib/mysql \
  mariadb:10.11

echo "=== 4. Levantando phpMyAdmin (http://localhost:8081) ==="
docker run -d \
  --name fcb-pma \
  --network fcb-network \
  --restart unless-stopped \
  -p 8081:80 \
  -e PMA_HOST=fcb-db \
  -e PMA_USER=wordpress \
  -e PMA_PASSWORD=wordpress \
  phpmyadmin:5.2

echo "=== 5. Esperando que MariaDB esté lista ==="
until docker exec fcb-db mysqladmin ping -uwordpress -pwordpress --silent; do
  echo "Esperando a MariaDB..."
  sleep 2
done
echo "MariaDB lista."

echo "=== 6. Levantando WordPress Apache (http://localhost:8080) ==="
docker run -d \
  --name fcb-wp \
  --network fcb-network \
  --restart unless-stopped \
  -p 8080:80 \
  -e WORDPRESS_DB_HOST=fcb-db \
  -e WORDPRESS_DB_NAME=wordpress \
  -e WORDPRESS_DB_USER=wordpress \
  -e WORDPRESS_DB_PASSWORD=wordpress \
  -e WORDPRESS_CONFIG_EXTRA="define('FS_METHOD', 'direct'); define('WP_MEMORY_LIMIT', '512M'); define('WP_MAX_MEMORY_LIMIT', '512M');" \
  -v fcb_wp_data:/var/www/html \
  -v fcb_wp_uploads:/var/www/html/wp-content/uploads \
  -v "$PROJECT_DIR":/var/www/html/wp-content/themes/fundacion-conrado-blanco \
  wordpress:php8.2-apache

wpcli() {
  docker run --rm \
    --network fcb-network \
    -v fcb_wp_data:/var/www/html \
    -v fcb_wp_uploads:/var/www/html/wp-content/uploads \
    -v "$PROJECT_DIR":/var/www/html/wp-content/themes/fundacion-conrado-blanco \
    -e WORDPRESS_DB_HOST=fcb-db \
    -e WORDPRESS_DB_NAME=wordpress \
    -e WORDPRESS_DB_USER=wordpress \
    -e WORDPRESS_DB_PASSWORD=wordpress \
    --user 33:33 \
    wordpress:cli "$@"
}

echo "=== 7. Esperando que WordPress inicialice archivos e instalando herramientas PDF ==="
sleep 5
docker exec -u 0 fcb-wp apt-get update >/dev/null 2>&1 || true
docker exec -u 0 fcb-wp apt-get install -y poppler-utils ghostscript >/dev/null 2>&1 || true

echo "=== 8. Instalando WordPress con usuario 'fcb' y contraseña 'fcb' ==="
if ! wpcli wp core is-installed --path=/var/www/html 2>/dev/null; then
  wpcli wp core install \
    --path=/var/www/html \
    --url="http://localhost:8080" \
    --title="Fundación Conrado Blanco" \
    --admin_user="fcb" \
    --admin_password="fcb" \
    --admin_email="admin@fundacionconradoblanco.local" \
    --skip-email
fi

echo "=== 9. Configurando idioma y enlaces permanentes ==="
wpcli wp language core install es_ES --path=/var/www/html || true
wpcli wp site switch-language es_ES --path=/var/www/html || true
wpcli wp rewrite structure '/%postname%/' --path=/var/www/html || true

echo "=== 10. Activando el tema local 'fundacion-conrado-blanco' ==="
wpcli wp theme activate fundacion-conrado-blanco --path=/var/www/html

echo "=== 11. Instalando y activando plugin 3D FlipBook ==="
wpcli wp plugin install interactive-3d-flipbook-powered-physics-engine --activate --path=/var/www/html || true
docker exec -u 0 fcb-wp chown -R www-data:www-data /var/www/html/wp-content/uploads 2>/dev/null || true
docker exec -u 0 fcb-wp chown -R 1001:1002 /var/www/html/wp-content/themes/fundacion-conrado-blanco 2>/dev/null || true

echo "=== 12. Creando páginas clave si no existen ==="
# Página Inicio
if ! wpcli wp post list --post_type=page --name=inicio --format=ids --path=/var/www/html 2>/dev/null | grep -q '[0-9]'; then
  HOME_ID=$(wpcli wp post create --post_type=page --post_title="Inicio" --post_name="inicio" --post_status=publish --porcelain --path=/var/www/html)
  wpcli wp option update show_on_front page --path=/var/www/html
  wpcli wp option update page_on_front "$HOME_ID" --path=/var/www/html
fi

# Página Biblioteca
if ! wpcli wp post list --post_type=page --name=biblioteca --format=ids --path=/var/www/html 2>/dev/null | grep -q '[0-9]'; then
  LIB_ID=$(wpcli wp post create --post_type=page --post_title="Biblioteca" --post_name="biblioteca" --post_status=publish --porcelain --path=/var/www/html)
  wpcli wp post meta set "$LIB_ID" _wp_page_template template-library.php --path=/var/www/html
fi

# Página Noticias
if ! wpcli wp post list --post_type=page --name=noticias --format=ids --path=/var/www/html 2>/dev/null | grep -q '[0-9]'; then
  NEWS_ID=$(wpcli wp post create --post_type=page --post_title="Noticias" --post_name="noticias" --post_status=publish --porcelain --path=/var/www/html)
  wpcli wp post meta set "$NEWS_ID" _wp_page_template page-noticias.php --path=/var/www/html
fi

echo "=== 13. Configurando Menú del Header idéntico a producción ==="
docker exec -i fcb-wp php -r "
require_once '/var/www/html/wp-load.php';

\$menu_name = 'Menú Principal';
\$menu_exists = wp_get_nav_menu_object(\$menu_name);
if (!\$menu_exists) {
    \$menu_id = wp_create_nav_menu(\$menu_name);
} else {
    \$menu_id = \$menu_exists->term_id;
    \$items = wp_get_nav_menu_items(\$menu_id);
    if (!empty(\$items)) {
        foreach (\$items as \$it) wp_delete_post(\$it->ID, true);
    }
}

\$locations = get_theme_mod('nav_menu_locations');
if (!is_array(\$locations)) \$locations = array();
\$locations['primary'] = \$menu_id;
set_theme_mod('nav_menu_locations', \$locations);

function add_item(\$menu_id, \$title, \$url, \$parent_id = 0) {
    return wp_update_nav_menu_item(\$menu_id, 0, array(
        'menu-item-title'   => \$title,
        'menu-item-url'     => \$url,
        'menu-item-status'  => 'publish',
        'menu-item-parent-id' => \$parent_id,
        'menu-item-type'    => 'custom',
    ));
}

add_item(\$menu_id, 'Inicio', home_url('/'));
\$m_fundacion = add_item(\$menu_id, 'La Fundación', home_url('/la-fundacion/'));
add_item(\$menu_id, 'Fundador', home_url('/la-fundacion/conradoblancogonzalez-fundador/'), \$m_fundacion);
add_item(\$menu_id, 'Esposa', home_url('/la-fundacion/esposa/'), \$m_fundacion);
add_item(\$menu_id, 'Madre', home_url('/la-fundacion/juliagonzalezprieto/'), \$m_fundacion);
add_item(\$menu_id, 'Padre', home_url('/la-fundacion/conradoblancoleon/'), \$m_fundacion);
add_item(\$menu_id, 'Patronato', home_url('/la-fundacion/patronato/'), \$m_fundacion);

\$m_actividades = add_item(\$menu_id, 'Actividades', '#');
\$years = array('2010', '2011', '2012', '2013', '2014', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025');
foreach (\$years as \$y) {
    add_item(\$menu_id, \$y, home_url('/' . \$y . '-2/'), \$m_actividades);
}

add_item(\$menu_id, 'Publicaciones', home_url('/publicaciones/'));
add_item(\$menu_id, 'Biblioteca', home_url('/biblioteca/'));

\$m_convocatorias = add_item(\$menu_id, 'Convocatorias', '#');
add_item(\$menu_id, 'Premio Nacional de Poesia Conrado Blanco Leon', home_url('/premio-nacional-de-poesia-conrado-blanco-leon/'), \$m_convocatorias);
add_item(\$menu_id, 'Premio Nacional de Poesia Infantil Charo Gonzalez', home_url('/premio-nacional-de-poesia-infantil-charo-gonzalez/'), \$m_convocatorias);

add_item(\$menu_id, 'Contacto', home_url('/contacto/'));
echo 'Menú de navegación configurado correctamente.\n';
" || true

echo ""
echo "=============================================================================="
echo " ¡WordPress Levantado y Configurado con Éxito!"
echo " Web: http://localhost:8080"
echo " Admin: http://localhost:8080/wp-admin"
echo " Usuario Admin: fcb"
echo " Contraseña Admin: fcb"
echo " phpMyAdmin: http://localhost:8081"
echo "=============================================================================="
