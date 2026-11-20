# PHP Coursework Exercises - MVC Architecture

Official repository for the **Web-Oriented Programming with PHP** course. This project contains **11 complete web applications**, all implemented following the architectural design pattern **Model-View-Controller (MVC)**, featuring responsive interactive user interfaces and zero-configuration SQLite (PDO) persistence.

---

## ðŸš€ GitHub Repository
[https://github.com/manolo700/Ejercicios-seguimiento-PHP](https://github.com/manolo700/Ejercicios-seguimiento-PHP)

---

## ðŸ“‹ Catalog of the 11 Exercises

| # | Exercise | Folder | Model | Controller | View |
|---|----------|--------|-------|------------|------|
| 1 | **Task List (To-Do)** | `01-task-list/` | `Task.php` | `TaskController.php` | `task_view.php` |
| 2 | **Tip Calculator** | `02-tip-calculator/` | `TipCalculator.php` | `TipController.php` | `tip_view.php` |
| 3 | **Secure Password Generator** | `03-password-generator/` | `PasswordGenerator.php` | `PasswordController.php` | `password_view.php` |
| 4 | **Expense Manager** | `04-expense-manager/` | `Expense.php` | `ExpenseController.php` | `expense_view.php` |
| 5 | **Booking & Reservation System** | `05-booking-system/` | `Booking.php` | `BookingController.php` | `booking_view.php` |
| 6 | **Note Taking App** | `06-notes-manager/` | `Note.php` | `NoteController.php` | `note_view.php` |
| 7 | **Interactive Event Calendar** | `07-event-calendar/` | `Event.php` | `EventController.php` | `event_view.php` |
| 8 | **Recipe Sharing Platform** | `08-recipe-platform/` | `Recipe.php` | `RecipeController.php` | `recipe_view.php` |
| 9 | **Memory Match Card Game** | `09-memory-game/` | `MemoryGame.php` | `GameController.php` | `game_view.php` |
| 10 | **Survey & Polling Platform** | `10-poll-platform/` | `Poll.php` | `PollController.php` | `poll_view.php` |
| 11 | **High-Precision Online Stopwatch** | `11-online-stopwatch/` | `Stopwatch.php` | `StopwatchController.php` | `stopwatch_view.php` |

---

## ðŸ—ï¸ MVC Architecture Design

Each exercise is organized under a decoupled and self-contained structure:

```text
[exercise-folder]/
â”œâ”€â”€ index.php                  # Front Controller / Entry point
â”œâ”€â”€ controllers/               # Handles HTTP requests & coordinates flow
â”‚   â””â”€â”€ [Name]Controller.php
â”œâ”€â”€ models/                    # Business logic & SQLite persistence
â”‚   â””â”€â”€ [Name].php
â”œâ”€â”€ views/                     # HTML5/CSS3/JS presentation layer
â”‚   â””â”€â”€ [name]_view.php
â””â”€â”€ data/                      # Local SQLite database and seed storage
    â””â”€â”€ .gitkeep
```

- **Model:** Encapsulates business logic, data rules, and SQL interactions using `PDO` prepared statements (with `PDO::ATTR_ERRMODE => ERRMODE_EXCEPTION` to prevent SQL injection). Tables are automatically created and seeded on first run if the SQLite file is empty.
- **View:** Clean, accessible, and responsive user interfaces built with **Bootstrap 5.3**, **Bootstrap Icons**, and **Chart.js**.
- **Controller:** Receives and validates incoming HTTP requests (`GET`/`POST`), interacts with the model, and passes formatted data to the view for rendering.

---

## ðŸ› ï¸ Environment Requirements

- **PHP 8.0** or higher (PHP 8.2 recommended).
- Standard extensions enabled (included by default in XAMPP):
  - `pdo`
  - `pdo_sqlite`
  - `session`
  - `json`

---

## ðŸ’» Installation & Execution Instructions

### Option 1: Using XAMPP (Apache)
1. Place or clone this repository into your XAMPP `htdocs` directory:
   ```bash
   cd c:\xampp\htdocs
   git clone https://github.com/manolo700/Ejercicios-seguimiento-PHP "SEGUIMIENTO PHP"
   ```
2. Start the Apache web server from the XAMPP Control Panel.
3. Open your browser and navigate to the root dashboard:
   ```text
   http://localhost/SEGUIMIENTO%20PHP/
   ```

### Option 2: Using the Built-in PHP Development Server
1. Open your terminal in the root directory of the project:
   ```bash
   cd "c:\xampp\htdocs\SEGUIMIENTO PHP"
   php -S localhost:8000
   ```
2. Navigate in your browser to:
   ```text
   http://localhost:8000
   ```

---

## ðŸ“Œ Module Feature Overview

1. **Task List (To-Do):**
   - Create tasks with priority tiers (High, Medium, Low).
   - Real-time completion toggle.
   - Status filters (All, Pending, Completed).
   - Single-task deletion and batch purge of completed tasks.

2. **Tip Calculator:**
   - Preset tip percentages (10%, 15%, 18%, 20%) or custom input.
   - Bill splitting among multiple people.
   - Optional rounding up to the nearest integer.
   - Per-person summary and calculation history log.

3. **Secure Password Generator:**
   - Adjustable length (6 to 64 characters).
   - Character sets toggle (uppercase, lowercase, numbers, special symbols).
   - Option to exclude ambiguous characters (0, O, 1, l, I).
   - Entropy meter and cryptographic strength feedback with one-click clipboard copy.

4. **Expense Manager:**
   - Daily expense logging with title, amount, category, date, and notes.
   - Monthly breakdown with statistics (daily average, monthly total, peak expense).
   - Visual category distribution progress bars.

5. **Booking & Reservation System:**
   - Professional service catalog with pricing and duration.
   - Real-time slot availability check to prevent double bookings.
   - Digital ticket generator with a unique confirmation code (e.g., `RES-A1B2C3`).
   - One-click booking cancellation.

6. **Note Taking App:**
   - Rich sticky-note style card creation with color tagging.
   - Category filtering (Work, Personal, Ideas, Study, Urgent).
   - Note pinning to keep important items at the top.
   - Instant search across note titles and content.

7. **Interactive Event Calendar:**
   - Monthly interactive grid calendar with previous/next month navigation.
   - Add, edit, and delete events with color categories.
   - Automatic reminder badge and upcoming 7-day alerts list.

8. **Recipe Sharing Platform:**
   - Detailed recipe cards (prep time, cook time, servings, step-by-step instructions, ingredients).
   - Search by recipe name or ingredients, plus category filters (Breakfast, Lunch, Dinner, Dessert, Snack).
   - Interactive recipe creation modal.

9. **Memory Match Card Game:**
   - Smooth 3D card flipping animations (`CSS preserve-3d`).
   - 3 difficulty levels: Easy (12 cards), Medium (16 cards), Hard (24 cards).
   - Active move counter, timer, and score calculation.
   - Hall of Fame leaderboard persisted in SQLite.

10. **Survey & Polling Platform:**
    - Dynamic poll creation with custom multiple-choice options.
    - Session-token vote protection (prevents duplicate voting).
    - Real-time percentage breakdowns and interactive bar charts powered by **Chart.js**.

11. **High-Precision Online Stopwatch:**
    - Digital time display with millisecond precision (hours, minutes, seconds, hundredths).
    - Start, Pause, Resume, and Reset controls.
    - Lap / split time recording with automatic fastest (green) and slowest (red) lap highlighting.
    - CSV export for recorded lap times and database session persistence.
