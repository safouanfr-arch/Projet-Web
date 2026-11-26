# Presse Ocean

French-language news site built for the ISFCE web project. The Vue 3 interface consumes a PHP API for browsing and filtering articles, reading full articles, and saving favorites in a session.

## Local Demo

Use PHP 8.1+ with PDO SQLite and DOM enabled, and a current Node.js LTS with npm. Run this once:

```sh
php Back-End/demo/seed.php
```

Start the API and UI in separate terminals:

```sh
php -S 127.0.0.1:8080 -t Back-End/public
```

```sh
cd Front-End
npm ci
npm run dev
```

Open `http://localhost:5173`. The fictional dataset includes articles, reporters and categories. Try searching for `port`, filtering by category, and adding an article to favorites.

To try login locally, start the PHP server with the environment variable `PRESS_DEMO=1` and sign in as `reader` with password `demo`. This account is for loopback demos. The default server does not forward login credentials to the former classroom service.

## Configuration

| Variable | Default and purpose |
| --- | --- |
| `PRESS_DSN` | The local SQLite demo database. Set this to a PDO DSN for the original MySQL schema. |
| `PRESS_DB_USER`, `PRESS_DB_PASSWORD` | Connection credentials for a configured PDO DSN. |
| `PRESS_BACKEND_URL` | `http://127.0.0.1:8080`, the backend destination for Vite's API and media proxy. |
| `PRESS_DEMO` | `1` enables the non-admin local demo account. |
| `PRESS_AUTH_URL` | Optional HTTPS URL for a trusted service implementing the classroom login response. |
| `PRESS_BANNER_URL` | Optional HTTPS JSON banner endpoint. |

The backend uses controllers, views and PDO models. The frontend is built with Vue Router and Vite. The original classroom dataset is separate from the synthetic demo.

```sh
cd Front-End
npm run build
```
