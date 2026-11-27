# Presse Ocean repository review

Reviewed baseline `59d1174`. The local demonstration uses an independently seeded SQLite database, optional loopback demo sign-in and a Vue frontend that proxies requests to the local PHP backend. The original course context and contributor credits belong in the repository documentation.

## Cleanup proposal

| Path or setting | Finding | Proposed treatment |
| --- | --- | --- |
| `.idea/` | Four tracked IDE metadata files have no role in the documented command-line demo. | Remove the tracked IDE files in a reviewed cleanup commit and ignore local IDE metadata. |
| `Front-End/README.md` and `Front-End/public/vite.svg` | The README is empty. The template favicon is still referenced by `Front-End/index.html`. | Keep instructions in the project README and replace the icon reference together with the icon. |
| `Back-End/public/media/` | 2,401 tracked image files. The source and redistribution terms for the classroom media collection are unverified. | Inventory referenced media, confirm rights and replace course images with assets with known provenance. Review both Vue and legacy PHP paths. |
| `Back-End/asset/database/menu.csv` | A classroom-era data asset read by the PHP template model. | Verify redistribution terms and retain the dependency or replace it with a synthetic menu before deleting the asset. |
| `Back-End/demo/` | Synthetic demonstration data and schema are used by the supported startup path. | Retain the seed script and schema. Keep generated SQLite state and local environment overrides out of commits. |
| `PRESS_DSN` and `PRESS_DB_*` | The optional database configuration reads connection settings from the process environment. | Point any legacy integration at an authorized environment and keep real data and credentials outside Git. |
| `PRESS_AUTH_URL`, `PRESS_BANNER_URL` and `PRESS_DEMO` | Optional external integrations and a local demo account. | Keep integrations unset in the standalone demo. Verify deployment policy before enabling an authentication endpoint. Use the demo account only on loopback. |
| `Front-End/vite.config.js` | Development proxy routing preserves same-origin browser behavior for the demo. | Keep the API and media routes when removing assets or changing backend configuration. Verify articles, search, demo login and session favorites after any cleanup. |

The tracked PHP, CSV and other selected text files were checked for common secret and email patterns. The binary image review was limited to counts and paths, so permissions and embedded metadata need a separate check. This branch documents proposed cleanup before a decision about what may remain public.

## Local baseline checks

The demo SQLite seed ran successfully. PHP syntax checks passed for all 36 tracked PHP files. The loopback articles request returned HTTP 200 with successful JSON, with external integrations disabled. Installation from the npm lockfile and the Vue production build passed.
