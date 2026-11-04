PANTRYPAL — SMART RECIPE FINDER
================================
HTML + CSS + JavaScript + PHP + MySQL/MariaDB college mini project.
No management dashboard. No AI API keys. No external packages or internet
required after installing XAMPP. Ingredient matching is deterministic, not AI.

RUN WITH XAMPP (WINDOWS)
1. Extract the ZIP. Copy smart_recipe into C:\xampp\htdocs\
   Confirm C:\xampp\htdocs\smart_recipe\index.html exists.
2. Start Apache and MySQL in XAMPP Control Panel.
3. Open http://localhost/phpmyadmin
4. Click Import, choose smart_recipe/database.sql and click Go.
   The script creates smart_recipe and inserts 12 recipes.
5. Edit config.php if your MySQL username, password or port differs.
   Defaults: root, empty password, host 127.0.0.1, port 3306.
6. Open http://localhost/smart_recipe/
   If Apache runs on port 8080: http://localhost:8080/smart_recipe/

REQUIREMENTS
PHP 8.0+ with PDO MySQL enabled; MySQL 5.7+ or MariaDB 10.4+;
modern Chrome, Edge, Firefox or Safari. Emoji appearance depends on your OS.
Do not double-click index.html or open it with Live Server: PHP needs Apache.

FEATURES
- 12 sample recipes with ingredient quantities and cooking steps.
- Ingredient selection and percentage coverage ranking.
- Search by recipe name, description or ingredient; diet and time filters.
- Favourites stored in MySQL per browser session.
- Recipe ratings and reviews stored in MySQL.
- One review per recipe per browser session; repeat submission updates it.
- Responsive recipe cards and accessible native recipe dialog.
- SQL prepared statements, CSRF tokens and safe text rendering.

MATCHING RULE
Selected ingredients are compared by exact ingredient ID. Recipes sharing
at least one selected ingredient are shown, highest coverage first.
Coverage = selected recipe ingredients / all recipe ingredients * 100.
Salt, oil and spices count. Optional listed ingredients also count.
Missing ingredients must be obtained before cooking. No automatic substitutions.

SESSION SCOPE
No registration/login. Favourites belong to the current PHP browser session;
clearing cookies or session expiry removes access to that session's favourites.
Reviews are visible to everyone using this local database. Names are public
within the project; do not enter private personal information.

FILES
index.html — HTML interface
assets/style.css — styling and responsive layouts
assets/app.js — filtering, matching, recipe dialog and fetch requests
api.php — PHP sessions, database queries, favourites and review validation
config.php — database configuration
database.sql — schema and sample data
PROJECT_REPORT.md — aim, modules, schema, algorithm and demo checklist

TROUBLESHOOTING
Database unavailable: start MySQL, import SQL, verify config.php.
No page: verify Apache port and folder nesting.
JSON/PHP error: use localhost with Apache, not file:// or Live Server.
Import error: use a compatible MySQL/MariaDB version with JSON support.
Re-importing SQL is safe for unchanged seed data (INSERT IGNORE).

VALIDATION
JavaScript syntax and archive integrity checked during creation. PHP/MySQL
not installed in the creation environment: live backend verification pending.
Follow PROJECT_REPORT.md demo checklist on XAMPP before college submission.
This is a local educational project; public hosting requires additional hardening.
