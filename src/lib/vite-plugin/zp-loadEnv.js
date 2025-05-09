import { loadEnv } from 'vite';
import path from 'path';
import { zeeltephp_postinstall } from './zp-post-install.js';

//      
/**
 * Load projects .env files. 
 * If .env-file is not found, values are generated from source path /your/path/htdocs/<your-svelte-project> 
 * @param {*} mode  from  @/vite.config.js  defineConfig(({ mode }) return { zeeltephp_loadEnv(mode); }
 * @returns 
 */
export function zeeltephp_loadEnv(mode) {
  try {
    // is ZeeltePHP .env already loaded?
    if (process.env?.ZP_MODE) return;  // avoid multiple calls

    // set ZP_MODE as flag to ensure ZeeltePHP is 1-time loaded.
    process.env.ZP_MODE = mode;

    console.log(`🐘 ZeeltePHP  - env.${mode}`)

    // load default vite/svelte --mode .env-file
    //    required setup for projects with ZeeltePHP
    process.env = loadEnv(mode, process.cwd(), '');

    // am I running myself ?
    //   is ZP_IS_SELFENV and set to .env in @zeeltephp/vite.config.js
    // -- process.env.ZP_MODE = process.env.npm_package_name == 'zeeltephp' ? true : false // self or consumer?

    // set ApiRouter required paths
    //    always presume from consumer project by default
    process.env.ZP_PATH_ZPLIB  = './src/lib/zplib/';
    process.env.ZP_PATH_ROUTES = './src/routes/';
    process.env.ZP_PATH_API    = process.env.ZP_IS_SELFENV ? './static/api/' : './node_modules/zeeltephp/dist/api/';

    // generate environment variables for ZeeltePHP, otherwise variables were loaded from .env-file
    // -- always run so missing variables are generated : if (!process.env?.PUBLIC_ZEELTEPHP_BASE) {
    if (true) {
      //    presuming project is installed at /path/to/htdocs/<your-svelte-project>
      const DIR_BUILD = process.env.BUILD_DIR || 'build-env';   // name of build folder
      const DIR_PROJECT = path.basename(process.cwd());           // path to project-root

      // set default (generated) paths for mode dev and build 
      // list < key, value, value >
      // list < env_variable, path_dev, path_build >
      const ENV_MINIMUM_VARS = [
        ['BUILD_DIR', '', DIR_BUILD],
        ['BASE', '', `/${DIR_PROJECT}/${DIR_BUILD}`],
        ['PUBLIC_ZEELTEPHP_BASE', 'http://localhost/' + DIR_PROJECT + '/static/api/', `/${DIR_PROJECT}/${DIR_BUILD}/api/`],
        ['ZEELTEPHP_DATABASE_URL', 'wordpress://../wordpress/', 'wordpress://../wordpress/']
      ];

      // am I in dev mode ?
      const DEV_MODE = mode == 'dev' || mode == 'development'; // --mode .env

      // set missing environment variables
      //     if value is undefined - its missing in .env-file
      for (let [env_var, path_dev, path_build] of ENV_MINIMUM_VARS) {
        let value = process.env[env_var];           // capture value from loaded .env-file
        if (value == undefined) {                   
          value = DEV_MODE ? path_dev : path_build  // set  value dev or build
          process.env[env_var] = value;             // save value to process.env
          console.log('  !', env_var, value);       // cli  generated value marked by !
        } else {
          console.log('   ', env_var, value);       // cli  value from .env-file
        }
      }
    }

    // run postinstall
    //     -  v1.0.2 - ensure only within consumer projects
    //     -  flag for @zeeltephp-env.development
    if (!process.env?.VITE_ZP_SELF) zeeltephp_postinstall();

  } catch (error) {
    console.error('🐘❌ ', error);
  }
}

