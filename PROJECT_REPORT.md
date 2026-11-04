# Smart Recipe Finder — PantryPal

## Aim
To develop an ingredient-based recipe discovery application using HTML, CSS, JavaScript, PHP and MySQL.

## Abstract
PantryPal helps users discover recipes from ingredients they already have. Users select pantry ingredients, search recipes and filter by diet or cooking time. Matching recipes are ranked by ingredient coverage. Each recipe includes quantities, servings and preparation steps. Users can save favourites and publish ratings and reviews. PHP handles database access, sessions and validation, while MySQL stores recipes, ingredients, favourites and reviews. The application uses a deterministic matching algorithm without external AI services.

## Modules
1. Ingredient selection and matching.
2. Recipe search and diet/time filters.
3. Recipe detail and cooking instructions.
4. Session-based favourites.
5. Community ratings and review submission.

## Technology mapping
| Technology | Role |
|---|---|
| HTML | Interface, form fields and recipe dialog |
| CSS | Responsive grid, cards, colours and typography |
| JavaScript | Ingredient ranking, filters, dialog and API requests |
| PHP | PDO database access, sessions, validation and CSRF protection |
| MySQL | Recipes, ingredients, links, favourites and reviews |

## Tables and relationships
- recipes: id (PK), title, description, emoji, color, minutes, servings, diet, steps (JSON).
- ingredients: id (PK), name (unique).
- recipe_ingredients: id (PK), recipe_id (FK), ingredient_id (FK), quantity; unique recipe/ingredient pair.
- favorites: id (PK), visitor_key, recipe_id (FK); unique visitor/recipe pair.
- reviews: id (PK), recipe_id (FK), visitor_key, display_name, rating, comment, created_at; unique recipe/visitor pair.

Recipes and ingredients have a many-to-many relationship through recipe_ingredients. Each recipe has many favourites and reviews.

## Matching algorithm
Let M be the number of recipe ingredients selected by the user and T be the total number of ingredients in that recipe. Coverage = M/T × 100. When ingredients are selected, show recipes with M > 0 and rank by coverage descending, then number of matches. With no ingredient selection, display all recipes ranked by average rating. Apply search, diet, time and favourites filters together. Matching runs in JavaScript using recipe records fetched from MySQL.

## API
- GET api.php?action=recipes: recipes, ingredients, favourites and session CSRF token.
- GET api.php?action=reviews&id=1: recent recipe reviews.
- POST api.php?action=favorite: recipe_id and csrf; toggles a favourite.
- POST api.php?action=review: recipe_id, name, rating, comment and csrf; creates or updates the session's review.

## Demonstration / test checklist
1. Import database.sql. Confirm 12 recipes and five tables.
2. Open the home page; verify all 12 recipes appear.
3. Select Tomato and Cooked rice. Confirm Tomato Rice shows 2/6 ingredients available (33%).
4. Search dosa; confirm Onion Dosa appears when other filters allow it.
5. Select Vegetarian and 15 minutes; verify results meet both constraints.
6. Clear filters and save a recipe; open My favourites and refresh the page. Confirm it remains saved in the same session.
7. Open a recipe; verify quantities, servings and steps.
8. Submit a review; confirm the review and average rating update.
9. Submit again for the same recipe; verify one review row is updated.
10. Enter HTML-looking text as a name; verify it displays as plain text.
11. Try a missing/invalid CSRF token; verify HTTP 403.
12. Stop MySQL; verify a readable setup error appears.
13. At mobile width verify filters and cards stack vertically.
14. Close the recipe dialog with Escape and the close button.

## Limitations
No permanent user accounts, allergen classification, nutrition estimates, image uploads or AI-generated recipes. Matching does not infer substitutions or ingredient amounts in the user's pantry. Listed time excludes preparing ingredients labelled cooked or ready-made. Session favourites are not portable across devices. Recipe emoji are decorative illustrations, not photographs.

## Result
The application provides ingredient-based recipe discovery with database-backed favourites and community reviews using all five required technologies.
