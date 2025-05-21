import { loadEnv } from 'vite';
import path from 'path';
import { zeeltephp_postinstall } from './zp-post-install.js';


/** Environment configuration constants */
const ENV_CONFIG = {
     PATHS: {
          API:    process.env.ZP_IS_SELFENV ? './static/api/' : './node_modules/zeeltephp/dist/api/',
          LIB:    './src/lib/zplib/',
          ROUTES: './src/routes/'
     },
     DEFAULTS: {
          BUILD_DIR: 'build',
          PUBLIC_ZEELTEPHP_BASE: '',
          DB_URLS: {
               WORDPRESS: 'wordpress://../../../wordpress/wp-load.php',
               MYSQL: 'mysql2:root@localhost/test'
          }
     }
};


/**
 * Loads and configures ZeeltePHP environment variables (.env)
 * Missing variables are generated from source path /htdocs/<your-svelte-project> 
 * @param {string} mode - Vite environment mode (development/production)
 */
export function zeeltephp_loadEnv(mode) {
     try {
          // Verify already loaded
          if (process.env?.ZP_MODE) return;
          process.env.ZP_MODE = mode;

          console.log(`🐘 ZeeltePHP - Environment: ${mode}`);

          // Load .env variables
          process.env = loadEnv(mode, process.cwd(), '');

          // Configure core paths
          set_core_paths();

          // Generate missing environment variables
          generate_missing_vars(mode);

          // Run post-install for consumer projects
          if (!process.env?.npm_package_name != 'zeeltephp') zeeltephp_postinstall();
          //console.log(process.env)
     } catch (error) {
          console.error('🐘❌ ', error);
     }
}

/**
 * Sets essential path configuration
 */
function set_core_paths() {
     Object.entries(ENV_CONFIG.PATHS).forEach(([key, value]) => {
          process.env[`ZP_PATH_${key}`] = value;
     });
}

/**
 * Generates missing required environment variables
 * @param {string} mode - Current environment mode
 */
function generate_missing_vars(mode) {
     const isDevMode   = ['dev', 'development'].includes(mode);
     const projectName = path.basename(process.cwd());
     const buildDir    = process.env.BUILD_DIR || ENV_CONFIG.DEFAULTS.BUILD_DIR;

     const envDefaults = [
          {
               key: 'BUILD_DIR',
               value: buildDir,
               devOnly: false
          },
          {
               key: 'BASE',
               value: isDevMode ? '' : `/${projectName}/${buildDir}`,
               devOnly: false
          },
          {
               key: 'PUBLIC_ZEELTEPHP_BASE',
               value: isDevMode 
                         ? `http://localhost/${projectName}/static/api/`
                         : `/${projectName}/${buildDir}/api/`,
               devOnly: false
          },
          {
               key: 'ZEELTEPHP_DATABASE_URL',
               value: ENV_CONFIG.DEFAULTS.DB_URLS.WORDPRESS,
               devOnly: true
          }
     ];

     envDefaults.forEach(({ key, value, devOnly }) => {
          if (!process.env[key] && (!devOnly || isDevMode)) {
                    process.env[key] = value;
                    console.log(`   ! ${key}: ${value}`);
          } else if (process.env[key]) {
                    console.log(`     ${key}: ${process.env[key]}`);
          }
     });
}