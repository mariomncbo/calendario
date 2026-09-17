# App Calendario Semanal
Aplicación Web minimalista para visualizar la semana en curso combinando eventos de Google Calendar y Google Tasks, con notificaciones diarias móviles.

## Arquitectura y Entorno
- **Entorno Local:** MAMP (Apache / MySQL / PHP).
- **Entorno Producción:** InfinityFree.
- **Autenticación:** Google OAuth (Login con Google).
- **Integraciones:** 
  - Google Calendar API.
  - Google Tasks API.
  - OneSignal (Push notifications para iOS/Web).

## Stack Tecnológico
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla).
- **Backend:** PHP nativo.
- **Base de Datos:** MySQL (Acceso vía PHP PDO con sentencias preparadas).

## Estructura del Proyecto
- `index.html` - Landing page pública (explicación de la app y botón de Login con Google).
- `dashboard.php` - Vista principal protegida (Calendario semanal y modal de ajustes).
- `backend/` - Endpoints PHP y lógica de base de datos/APIs.
- `frontend/scripts/` - Lógica del cliente en JavaScript.
- `frontend/styles/` - Hojas de estilo CSS.

## Convenciones de Código y Diseño
- **Nomenclatura:** `snake_case` para variables y funciones.
- **Idioma:** IDs, clases CSS y variables en español.
- **Diseño UI:** Estética limpia, minimalista y editorial. Interfaz libre de ruido visual.
- **HTML/CSS:** Las clases en HTML son exclusivas para estilos CSS. Usar IDs para manipular con JS.
- **Errores:** Mostrar advertencias y errores del cliente únicamente por consola (`console.error`).

## Restricciones Críticas (No hagas)
- **NO** propongas Node.js, Docker, ni frameworks complejos (React/Vue/Laravel). El stack es estrictamente Vanilla JS y PHP nativo.
- **NO** instales dependencias vía Composer o npm sin solicitar confirmación previa.
- **NO** toques el archivo `.env`.
- **NO** incluyas ni hardcodees API Keys (Google, OneSignal) en el código fuente.
- **NO** modifiques `opencode.json` sin mi OK.

## Reglas de Backend (APIs PHP)
Todos los archivos `.php` en `backend/` deben seguir estrictamente este flujo:
1. `header('Content-Type: application/json');`
2. Inclusión de utilidades (`require_once 'conexion.php';`).
3. `session_start();`
4. Inicializar `$respuesta = ['success' => false, 'errores' => []];`.
5. Validar método HTTP (Retornar HTTP 405 si es incorrecto).
6. Validar sesión (Retornar HTTP 401 si no hay usuario logueado).
7. Validar parámetros de entrada.
8. Ejecutar lógica en bloque `try/catch (PDOException $e)`.
9. Retornar `echo json_encode($respuesta);` y finalizar ejecución con `exit;`.

## Reglas de Frontend (Vistas y JS)
- **Protección de rutas:** Toda vista privada (ej. `dashboard.php`) debe iniciar con `session_start();` y redirigir a `index.php` si no existe la sesión de Google.
- **JavaScript:** Todo script que manipule el DOM debe estar envuelto en `window.onload = function() {}` .

## Flujo de Trabajo
1. Analiza mi solicitud y propón un plan breve antes de escribir código masivo.
2. Aborda una sola tarea o archivo a la vez.
3. Al terminar, resume los cambios para que pueda revisarlos.
4. Si hay dudas sobre la implementación de las APIs de Google o OneSignal, detente y pregunta.