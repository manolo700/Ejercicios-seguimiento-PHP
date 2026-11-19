# Ejercicios de Seguimiento PHP - Arquitectura MVC

Repositorio oficial para la materia de **Programación Orientada a la Web con PHP**. Este proyecto reúne **11 aplicaciones web completas**, todas implementadas bajo el patrón de diseño arquitectónico **Modelo-Vista-Controlador (MVC)**, con interfaces interactivas, responsivas y persistencia autónoma mediante SQLite (PDO).

---

## 🚀 Repositorio en GitHub
[https://github.com/manolo700/Ejercicios-seguimiento-PHP](https://github.com/manolo700/Ejercicios-seguimiento-PHP)

---

## 📋 Catálogo de los 11 Ejercicios

| # | Ejercicio | Carpeta | Modelo | Controlador | Vista |
|---|-----------|---------|--------|-------------|-------|
| 1 | **Lista de Tareas** | `01-lista-tareas/` | `Task.php` | `TaskController.php` | `task_view.php` |
| 2 | **Calculadora de Propinas** | `02-calculadora-propinas/` | `TipCalculator.php` | `TipController.php` | `tip_view.php` |
| 3 | **Generador de Contraseñas** | `03-generador-contrasenas/` | `PasswordGenerator.php` | `PasswordController.php` | `password_view.php` |
| 4 | **Gestor de Gastos** | `04-gestor-gastos/` | `Expense.php` | `ExpenseController.php` | `expense_view.php` |
| 5 | **Sistema de Reservas** | `05-sistema-reservas/` | `Booking.php` | `BookingController.php` | `booking_view.php` |
| 6 | **Gestor de Notas** | `06-gestor-notas/` | `Note.php` | `NoteController.php` | `note_view.php` |
| 7 | **Calendario de Eventos** | `07-calendario-eventos/` | `Event.php` | `EventController.php` | `event_view.php` |
| 8 | **Plataforma de Recetas** | `08-plataforma-recetas/` | `Recipe.php` | `RecipeController.php` | `recipe_view.php` |
| 9 | **Juego de Memoria** | `09-juego-memoria/` | `MemoryGame.php` | `GameController.php` | `game_view.php` |
| 10 | **Plataforma de Encuestas** | `10-plataforma-encuestas/` | `Poll.php` | `PollController.php` | `poll_view.php` |
| 11 | **Cronómetro Online** | `11-cronometro-online/` | `Stopwatch.php` | `StopwatchController.php` | `stopwatch_view.php` |

---

## 🏗️ Arquitectura MVC Empleada

Cada ejercicio cuenta con su propia estructura independiente y desacoplada:

```text
[ejercicio]/
├── index.php                  # Front Controller / Enrutador
├── controllers/               # Maneja la petición y orquesta el flujo
│   └── [Name]Controller.php
├── models/                    # Lógica de negocio y persistencia
│   └── [Name].php
├── views/                     # Presentación e interfaz HTML/CSS/JS
│   └── [name]_view.php
└── data/                      # Base de datos SQLite y recursos locales
    └── .gitkeep
```

- **Modelo (Model):** Gestiona los datos, las reglas de negocio y las consultas SQL (usando `PDO::ATTR_ERRMODE => ERRMODE_EXCEPTION` y sentencias preparadas contra inyección SQL). Las tablas se crean y auto-pueblan automáticamente si la base de datos no existe.
- **Vista (View):** Componentes visuales limpios, accesibles y responsivos construidos con **Bootstrap 5.3**, **Bootstrap Icons** y **Chart.js**.
- **Controlador (Controller):** Recibe las peticiones HTTP (`GET`/`POST`), valida los datos de entrada, invoca los métodos del modelo y prepara la información que la vista renderiza.

---

## 🛠️ Requisitos del Entorno

- **PHP 8.0** o superior (recomendado PHP 8.2).
- Extensiones estándar habilitadas (incluidas por defecto en XAMPP):
  - `pdo`
  - `pdo_sqlite`
  - `session`
  - `json`

---

## 💻 Instrucciones de Instalación y Ejecución

### Opción 1: Con XAMPP (Apache)
1. Coloca o clona esta carpeta en tu directorio `htdocs` de XAMPP:
   ```bash
   cd c:\xampp\htdocs
   git clone https://github.com/manolo700/Ejercicios-seguimiento-PHP "SEGUIMIENTO PHP"
   ```
2. Inicia el servidor Apache desde el panel de control de XAMPP.
3. Abre tu navegador y accede al portal central:
   ```text
   http://localhost/SEGUIMIENTO%20PHP/
   ```

### Opción 2: Con el Servidor Integrado de PHP
1. Abre tu terminal o consola en la carpeta raíz del proyecto:
   ```bash
   cd "c:\xampp\htdocs\SEGUIMIENTO PHP"
   php -S localhost:8000
   ```
2. Abre tu navegador en:
   ```text
   http://localhost:8000
   ```

---

## 📌 Detalle de Funcionalidades por Módulo

1. **Lista de Tareas:**
   - Creación de tareas con niveles de prioridad (Alta, Media, Baja).
   - Marcado de completado / desmarcado en tiempo real.
   - Filtros por pestañas (Todas, Pendientes, Completadas).
   - Eliminación individual y limpieza masiva de completadas.

2. **Calculadora de Propinas:**
   - Selección rápida de porcentajes habituales (10%, 15%, 18%, 20%) o personalizado.
   - División de la cuenta entre múltiples personas.
   - Opción para redondear al entero superior.
   - Desglose del total por persona y registro histórico de cuentas.

3. **Generador de Contraseñas Seguras:**
   - Ajuste de longitud de 6 a 64 caracteres.
   - Selección de juegos de caracteres (mayúsculas, minúsculas, números, símbolos).
   - Filtro de caracteres ambiguos (0, O, 1, l, I).
   - Medidor de entropía y nivel de seguridad criptográfica con botón de copiado al portapapeles.

4. **Gestor de Gastos:**
   - Registro de gastos diarios con concepto, monto, categoría y notas.
   - Desglose mensual y cálculo automático de promedio diario, total mensual y mayor gasto.
   - Barras de distribución porcentual por categorías.

5. **Sistema de Reservas:**
   - Catálogo de servicios profesionales con precios y duraciones.
   - Verificación de disponibilidad de horarios en tiempo real (evita colisiones de citas).
   - Emisión de ticket digital con código único de confirmación (ej: `RES-A1B2C3`).
   - Opción de cancelación de citas.

6. **Gestor de Notas:**
   - Creación y edición de notas tipo post-it con código de color.
   - Filtro por categorías (Trabajo, Personal, Ideas, Estudio, Urgente).
   - Fijación de notas importantes al inicio (`pinned`).
   - Buscador por texto en títulos y contenidos.

7. **Calendario de Eventos:**
   - Vista mensual interactiva tipo calendario de cuadrícula.
   - Registro, edición y eliminación de eventos con colores personalizados.
   - Recordatorios automáticos con panel de alertas para los próximos 7 días.

8. **Plataforma de Recetas:**
   - Fichas culinarias completas (ingredientes, preparación paso a paso, fotos, tiempo, porciones).
   - Buscador por ingredientes y filtro por categoría (Desayuno, Almuerzo, Cena, Postre, Snack).
   - Formulario para publicar nuevas recetas en la comunidad.

9. **Juego de Memoria:**
   - Volteo de cartas con animación 3D (`CSS preserve-3d`).
   - 3 dificultades: Fácil (12 cartas), Medio (16 cartas) y Difícil (24 cartas).
   - Contador de movimientos, cronómetro activo y cálculo de puntaje.
   - Tabla de récords (Hall of Fame) con los mejores puntajes guardados.

10. **Plataforma de Encuestas:**
    - Creación dinámica de encuestas con opciones múltiples.
    - Votación protegida por token de sesión único (un voto por usuario).
    - Estadísticas porcentuales y generación de informes visuales con gráficos interactivos de **Chart.js**.

11. **Cronómetro Online:**
    - Display digital de alta precisión (horas, minutos, segundos y centésimas).
    - Controles de Iniciar, Pausar, Reanudar y Reiniciar.
    - Registro de vueltas / tiempos parciales con detección automática de vuelta más rápida (verde) y más lenta (rojo).
    - Exportación de las vueltas registradas a archivo CSV y guardado en base de datos.

