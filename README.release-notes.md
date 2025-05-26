**v1.0.3 (rc1)**   2025-05-26
- **Installation & Key Path Changes**
  - Previous installations should continue to work, but two manual changes are required:
    - In `vite.config.js`, set `zeeltephp(mode)` before `sveltekit()`.
    - Renaming of 2 Key Paths:
      - /src/lib/zplib: is now /src/lib_php - move your php files and remove /zplib.
      - /zp-log: is now /php_log - remove /zp-log.
- **General Improvements**
  - Improved source code documentation (JS & PHP).
  - Refactored some logic in preparation for new features.
- **README** 
  - Updated for v1.0.3 with improved styling.
- **Vite-Plugin**
  - Completely overhauled for readability, documentation, and future extensibility.
-  **ZP Demo / ZP Dev**
  - Improved styling and added documentation in src.
  - Added new tabs:
    - DB - debug/test database connections.
    - ENV- view .env configuration
- **PHP api**
  - **/core**
    - Prepared to support additional +.php files in future releases.
    - Introduced new startup and processing logic.
  - **/lib**
    - Removed unused PHP files to focus on core functionality and documentation.
    - **/db/db.mysql2.php** completely rewritten.
    - **/io/dir.php**
      - `zp_scandir()` now supports RegExp for name matching.
      - Added `zp_scandirRecursiveDown()` and `zp_scandirRecursiveUp()`.
- **env.ZEELTEPHP_DATABASEURL**
  - For security, a default DatabaseURL is no longer auto-generated.
  - To use a DB provider, set `ZEELTEPHP_DATABASEURL` in .env.

---

**v1.0.2 (rc1)**   2025-05-14
- **New Installation steps**
  - Previous installations should continue to work. If not, please remove and re-install ZeeltePHP.
- **README**
  - updated for 1.0.2 (rc1). 
- **Core**
  - Code cleanup, improved documentation, various enhancements, removal of duplicated code, cleaner CLI log output, internal variable renaming, updates to vite-plugin, `ZP_ApiRouter`, `ZP_EventDetails`, `zp_fetch_api()`, and PHP-@/api/core.
- **/zp_demo/**         
  - Can now be used as template, skeleton and debugger for your project.
  - To use: Copy `/zpdemo` into your `/routes`, run `npm run dev`, and open in your browser.
  - *Green lights?* Great! The minimal installation is complete and ZeeltePHP is running (route and `.page.server.php` are detected).
  - *Other lights?* Check your installation and ensure your Svelte and PHP code is present (`+.php`).
- **ZPDev**         
  - Full live demo of use-cases; also serves as an initial helper/debugger to verify setup or identify issues.
  - Debug any route by adding `<ZPDev />` in your `*.svelte`.
  - Works directly with `/zpdemo`.
  - Includes an action name/value editor for testing PHP code in any route.
- **/src/routes/**
  - Documentation and live demo parts moved into ZPDev as a "debugger feature".
  - `/src/routes/send-data` and basic examples moved into ZPDev + /zpdemo.
- **ZP_ApiRouter/PHP**    
  - Added support for grouped routes e.g. /(admin)/admin
- **zeeltephp_loadEnv()**
  - Changed default DB provider to WordPress (was MySQL).
  - Updated default path to 'wordpress://../../../wordpress/wp-load.php'

-----

**v1.0.1 (rc1)** 2025-05-07
- **README**
  - Rewritten to meet v1.0.0 and v1.0.1 requirements, with updated examples, installation steps, .. 
  - Added `README.release-notes`.
- **ZPDev**         
  - A component to help debug `+page.server.php` files.
- **trustedDependencies and postinstall**
  - No longer required; postinstall is now handled via `zeeltephp_loadEnv()` during `npm run`.
- **Internal** 
  - post-build; Added 'sameLine' for console.log.
  - zeelte-init.php; Moved code to `zp-bootstrap.php` and `zp-paths` for better organization and readability.
  - import from 'zeeltephp';

---

**v1.0.0 (rc1)** 2025-05-03
- Initial v1.0.0(rc1) release (public)
