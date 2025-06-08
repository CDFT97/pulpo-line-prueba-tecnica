# Pulpoline Test

Este proyecto tiene como objetivo principal demostrar la integración y el funcionamiento de la API de WeatherAPI en una aplicación Laravel. Se ha puesto especial énfasis en la implementación de las mejores prácticas y principios de desarrollo en Laravel, abarcando conceptos clave como:

-   **Form Requests**: Para validación y autorización de peticiones.
-   **Middleware**: Para la lógica de pre-procesamiento de peticiones.
-   **Services**: Para encapsular la lógica de negocio.
-   **Repositories**: Para la abstracción de la capa de datos.
-   **Testing**: Con tests unitarios y funcionales para asegurar la calidad del código.
-   **Cache**: Para optimizar el rendimiento de la aplicación.
-   **Repository Pattern**: Para desacoplar la lógica de acceso a datos.
-   **Multi-language**: Soporte para internacionalización.
-   **Traits**: Para reutilización de código.
-   **Swagger**: Para la documentación interactiva de la API.
-   **Roles and Permissions**: Para la gestión de acceso de usuarios.

---

## ⚙️ Requisitos

Antes de comenzar, asegúrate de tener instalados los siguientes componentes:

-   **PHP**: Versión 8.2 o superior.
-   **Laravel**: Versión 10 o superior (aunque el original menciona Laravel 12, Laravel 10 es más común a la fecha de este README y compatible con PHP 8.2+).
-   **Composer**: Gestor de dependencias de PHP.
-   **MySQL**: Base de datos relacional.
-   **WeatherAPI API Key**: Una clave de API válida de [WeatherAPI](https://www.weatherapi.com/).

---

## 🚀 Instalación

Sigue los pasos a continuación para configurar y ejecutar el proyecto en tu entorno local:

1.  **Clonar el repositorio**:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd pulpoline-test # O el nombre de tu carpeta
    ```
2.  **Crear el archivo `.env`**:
    Copia el archivo de ejemplo y genera tu propio archivo de configuración:
    ```bash
    cp .env.example .env
    ```
3.  **Configurar WeatherAPI Key**:
    Abre el archivo `.env` y añade tu clave de API de WeatherAPI:
    ```dotenv
    WEATHER_API_KEY="TU_CLAVE_DE_WEATHERAPI_AQUI"
    ```
4.  **Instalar dependencias de Composer**:
    ```bash
    composer install
    ```
5.  **Generar la clave de la aplicación**:
    ```bash
    php artisan key:generate
    ```
6.  **Ejecutar migraciones**:
    Esto creará las tablas necesarias en tu base de datos:
    ```bash
    php artisan migrate
    ```
7.  **Ejecutar seeders**:
    Esto poblará la base de datos con datos de prueba:
    ```bash
    php artisan db:seed
    ```
8.  **Iniciar el servidor de desarrollo**:
    ```bash
    php artisan serve
    ```
    El servidor estará disponible usualmente en `http://127.0.0.1:8000`.

---

## 📄 Documentación (Swagger)

El proyecto incluye documentación de la API generada con Swagger para facilitar la exploración y el uso de los endpoints.

1.  Asegúrate de tener el plugin [L5-Swagger](https://github.com/DarkaOnline/L5-Swagger) configurado.
2.  Genera la documentación ejecutando:
    ```bash
    php artisan l5-swagger:generate
    ```
    La documentación generada se encontrará en el directorio `resources/docs/swagger.json`.
3.  Para acceder a la documentación interactiva en tu navegador, visita:
    ```
    /api/documentation
    ```
    (Por ejemplo, `http://127.0.0.1:8000/api/documentation`)

---

## 📬 Postman Collection

Para tu conveniencia, también se proporciona una colección de Postman en la raíz del proyecto. Esta colección te permite probar los endpoints de la API de manera rápida y eficiente. Busca el archivo con extensión `.postman_collection.json`.

---

## 🧪 Tests

El proyecto ha sido desarrollado con una cobertura de tests unitarios y funcionales. Puedes encontrar los tests en el directorio `tests`.

Para ejecutar todos los tests, utiliza el siguiente comando:

```bash
php artisan test
```

## 🌐 Demo Online
Puedes explorar una versión de la API desplegada en línea en la siguiente URL:
https://weatherapi-app.laravel.cloud/api

Acceder a la documentación de la API en línea en la siguiente URL:
https://weatherapi-app.laravel.cloud/api/documentation

Nota: Al ser un proyecto de prueba alojado en un servidor gratuito, la disponibilidad y el rendimiento pueden variar. Es posible que el servidor deje de funcionar en cualquier momento.


## 📝 Licencia
Este proyecto está licenciado bajo la licencia MIT.


## 📄 Guía rápida de uso

### Endpoints de Autenticación (AUTH)

1.  **Registro:** Permite crear una nueva cuenta de usuario.
2.  **Login:** Permite iniciar sesión con una cuenta existente y obtener un token de autenticación.
3.  **Logout:** Permite cerrar la sesión del usuario, invalidando el token de autenticación.
4.  **Upgrade To Premium:** Permite actualizar el rol/licencia del usuario de "free" a "premium".

### Endpoints del Clima (WEATHER)

1.  **Get Weather By City:** Permite obtener la información meteorológica de una ciudad específica.
2.  **Get Recent Searches:** Permite obtener el historial de las últimas búsquedas de clima realizadas por el usuario.
3.  **Get Favorites:** Permite obtener la lista de ciudades marcadas como favoritas por el usuario.
4.  **Toggle Favorite:** Permite agregar o eliminar una ciudad de la lista de favoritos del usuario.

### Restricciones de Acceso

*   Al registrarse, el usuario obtiene el rol/licencia **"free"**, el cual **NO** permite acceder a ningún endpoint de **WEATHER**.
*   Para poder acceder a los endpoints de **WEATHER**, el usuario debe actualizar su rol a **"premium"** utilizando el endpoint **"Upgrade To Premium"**.
*   Todos los endpoints de **WEATHER** requieren **autenticación**. Por lo tanto, el usuario debe iniciar sesión (ejecutar el endpoint **"Login"**) y proporcionar un token de autenticación válido para poder acceder a ellos.
* Por cierto, la app es multi-idioma, puedes pasar el query param `lang` para cambiar el idioma de la respuesta. (aunque por ahora solo soporta inlges y español) lang=en o lang=es