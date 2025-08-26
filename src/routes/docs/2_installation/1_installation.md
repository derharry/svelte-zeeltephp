# Installation 

## Quick start
Follow the installation steps 1 - 5. 
Then
- Copy `/zpdev` into your `/routes` and see if it works.
- Start using `zp_fetch()`.


## Installation

1. **Create your SvelteKit project**  
   ```
   npx sv create myZPproject
   ```
   - Be sure to use `adapter-static`
   - You have 2 options where to save your project:
     1. in your local httpd `DOCUMENT_ROOT` `/htdocs` directory. (default)
     2. or anywhere and use `php.exe` directly if configured.

2. **Install ZeeltePHP**  
   ```
   npm add zeeltephp
   ```

3. **Add Vite Plugin**  
   Update your `vite.config.js`:
   ```js
   import { sveltekit } from '@sveltejs/kit/vite';
   import { defineConfig } from 'vite';
   import { zeeltephp } from 'zeeltephp/vite-plugin';   // add
   
   export default defineConfig(({ mode }) => {     // add mode
      return {
         plugins: [
            zeeltephp(mode),   // add, before sveltekit()
            sveltekit(),
         ]
      }
   });
   ```
   <ins>Note:</ins> the vite-plugin creates, if not exist, following paths in your project:
   ```sh
   /src/lib_php            # Shared PHP library for your +.php files
   /src/routes/+layout.js  # Required by adapter-static
   /static/api/index.php   # PHP api entry point
   /php_log                # PHP error and log output
   ```

4. **Start Development (Optional)**  
   ```
   npm run dev
   ```
   Open `http://localhost:5173/myZPproject` to check if your app is working.
   <br>If you encounter issues, check the CLI output for 🐘 ZeeltePHP and ensure the required paths have been created.

5. **Demo & Debug (Optional)**  
   Copy `/zpdev/` into `/src/routes/` and verify it runs in development mode.
   <br>Green icons and receiving responses? Have fun `SveltePHP'ing`.

6. **Configure for Build**  
   Update `svelte.config.js` to use `.env` variables:
   ```js
   import adapter from '@sveltejs/adapter-static';

   const config = {
      kit: {
         adapter: adapter({
            pages:  process.env.BUILD_DIR,  // add
            assets: process.env.BUILD_DIR,  // add
         }),
         paths: { 
            base: process.env.BASE          // add
         }
      }
   };
   export default config;
   ```

   (Optional)
   ```
   npm run build
   ```
   Open `http://localhost/myZPproject/build/` and see if your build is working. 


7. **Configure `.env` for Build or Final Production Deployment**  
   Configure your `.env.production` for deployment (e.g. export/FTP).
   See `Description/.env Configuration` for more details.
   ```
   BUILD_DIR=../build-prod                 # build-output folder; e.g. http://localhost/build-prod
   BASE=/build-prod                        # starting from `DOCUMENT_ROOT`; e.g. http://www.domain.com/build-prod
   PUBLIC_ZEELTEPHP_BASE=/build-prod/api/  # Same as BASE but ends with `api/`
   ```
   <ins>Note:</ins> If no `.env.production` is specified, the variables are auto-generated and will run by default as: 
         `http://localhost/myZPproject/build` or `https://www.domain.com/myZPproject/build`.


---


## Uninstall

1. Uninstall ZeeltePHP:
   ```
   npm remove zeeltephp
   ```
2. Manually remove the auto-created paths if not needed.


------


## Minimum Requirements

- Svelte 5
- SvelteKit 2
- SvelteKit Adapter Static 3

- Local Httpd (Apache, Nginx, etc.) configured with PHP 8 
  or PHP only /path/to/php.exe without httpd. See [PHP.exe](#phpexe) for details.


---


### Example Environments

**Production**  `https://www.example.com/<my-build>`
- Linux, Apache 2.4, PHP 8.3, MariaDB

**Development** `http://localhost/<my-project>/<my-build>` 
- bun, npm, or others
- XAMPP with PHP ^8.0, MariaDB (like XAMPP-ApacheFriends)
  or using /path/to/php.exe directly without a httpd