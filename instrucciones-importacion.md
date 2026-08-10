# Instrucciones de Importación de Libros en JSON

Este documento detalla cómo dar formato al archivo JSON para realizar la importación masiva de libros en el panel de administración del tema de la Fundación Conrado Blanco.

## Formato del Objeto JSON

Cada libro se define como un objeto dentro de un array JSON de la siguiente forma:

```json
[
  {
    "title": "Capiteles para la historia Bañezana 11",
    "pdf": "https://fundacionconradoblanco.com/wp-content/uploads/2025/02/capiteles-XI-LIBRO-b.pdf",
    "ebook": "https://fundacionconradoblanco.com/3d-flip-book/capiteles-para-la-historia-banezana-11/",
    "cover": "https://fundacionconradoblanco.com/wp-content/uploads/2025/02/Capiteles11-1.png",
    "edition": "Edición 11 (2025)",
    "cat": "Capiteles para la historia Bañezana"
  }
]
```

## Campos Disponibles

| Clave | Descripción | Obligatorio | Acción Interna |
|---|---|---|---|
| `title` | El título descriptivo completo del libro. | **Sí** | Título del post. |
| `pdf` | URL absoluta del archivo PDF original en producción. | No | Se descarga localmente a la biblioteca de medios. |
| `ebook` | URL absoluta del visor interactivo 3D FlipBook. | No | Se guarda en los metadatos y se asocia al botón "Ver". |
| `cover` | URL absoluta de la portada del libro. | No | Se descarga localmente y se asigna como Imagen Destacada. |
| `edition` | Texto descriptivo de la edición o el año. | No | Muestra la información del año en las tarjetas de la web. |
| `cat` | Categoría o Colección del libro para organizarlo. | No | Asigna el término en la taxonomía de categorías de libros. |

## Instrucciones de Uso

1. Copia tu listado de libros con el formato anterior.
2. Accede al escritorio de WordPress y ve a la sección **Gestión Biblioteca** en la barra lateral.
3. En la caja **Importar Libros mediante JSON**, pega el array completo en el cuadro de texto.
4. Haz clic en **Comenzar Importación**.
5. El sistema descargará automáticamente los PDFs y portadas al servidor local, creará los libros y los clasificará en sus colecciones correspondientes.
