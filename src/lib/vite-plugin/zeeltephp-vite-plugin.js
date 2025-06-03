// zp-env-plugin.js
import { loadEnv } from 'vite';                 // used at zeeltephp_loadEnvironment()
import { exec_task } from './zp.vite.tools.js'; // used at process_tasks()
import path from 'path'; // used at postinstall(), postbuild(), zp_info
import fs from 'fs';     // used at postinstall(), postbuild()


/** Environment configuration settings and defaults */
const zp_info = {
     MODE: {
          mode:        null,
          isDevMode:   false,
          isBuildMode: false,
          // -- closeBundleRuntimes: 0, --> handles via process.env at closeBundle-handler
          projectName: path.basename(process.cwd()),
          DIR_PERMISSIONS:  0o755, // not used yet
          FILE_PERMISSIONS: 0o644, // not used yet
     },
     ENV_LOADED:   {},
     ENV_DEFAULTS: {
          BUILD_DIR:  'build',
          BASE:      '/build',
          PUBLIC_ZEELTEPHP_BASE: '/build/api/'
     },
     ENV_WHITELIST: ['BASE', 'VITE_', 'PUBLIC_', 'ZEELTEPHP_'],
     PATHS: {
          API:    process.env.ZP_IS_SELFENV ? './static/api/' : './node_modules/zeeltephp/dist/api/',
          LIB:    './src/lib_php/',
          ROUTES: './src/routes/'
     },
     POSTBUILD_TASKS: [
          { name: 'API',    todo: 'copy',  dst: '/api',                   },
          { name: 'LIB',    todo: 'copy',  dst: '/api/zeeltephp/lib_php', },
          { name: 'ROUTES', todo: 'copy',  dst: '/api/zeeltephp/routes',  filter: (file) => file.endsWith('.php') },
          { name: 'LOG',    todo: 'mkdir', dst: '/api/zeeltephp/php_log'  },
          { name: 'ENV',    todo: 'file',  dst: '/api/zeeltephp/.env'     }
     ],
     POSTINSTALL_TASKS: [
          { todo: 'mkdir',  dst: './php_log'     },
          { todo: 'mkdir',  dst: './src/lib_php' },
          { todo: 'copy',   dst: './src/routes/+layout.js', src: './node_modules/zeeltephp/dist/templates/src/routes/+layout.js' },
          { todo: 'copy',   dst: './static/api',            src: './node_modules/zeeltephp/dist/templates/static/api' }
     ]
};


/**
 * ZeeltePHP Vite Plugin
 * ---------------------
 * Integrates ZeeltePHP into Vite/SvelteKit projects by:
 * 1. Loading/generating environment variables
 * 2. Handling post-build operations for PHP integration
 * 
 * Usage in vite.config.js:
 * plugins: [zeeltephp(mode)]
 * 
 * Version 2 - 2025.05.24
 *
 * @param {string} mode - Vite mode (development/production)
 * @returns {import('vite').Plugin} Vite plugin object
 */
//replaces previous versions: @/postinstall, @/trustedDependencies, @/zeeltephp-post-install.sh
export function zeeltephp(mode) {
     //console.log('🐘 ZeeltePHP plugin 🚀',);
     
     // load Environment here on Top-Level -- see Info-2025.05.24 for details
     zeeltephp_loadEnvironment('init', mode);
     
     return {
          name: 'zeeltephp-vite-plugin',

          config(config, { mode, command }) {
               /*
                    2025.05.24 - Info  - load the .env-file via 'mode' instead at Top-Level
                    -- zeeltephp_loadRealEnv(mode);
                    I want to load the .env configuration file from here, so its not required to pass 'mode' from vite.config.js. 
                    Unfortunatally this is not possible (or could not find the solution). 
                    As per design, config() and configResolved() is loaded after SvelteKit evaluated svelte.config.js and then vite.config.js. 
                    For the time beeing - keep 'mode' passed untill we find another solution.
               */
               zp_info.MODE.mode        = mode;
               // 2025-05-30 - in rare cases the run dev|build generated wrong base-paths in consumer project
               //    this part moved to loadEnvironment() via process.env.NODE_ENV.
               // -- zp_info.MODE.isDevMode   = command === 'serve';
               // -- zp_info.MODE.isBuildMode = command === 'build';
               zeeltephp_loadEnvironment('config', mode);

               // run the installer at runtime instead package.json/postinstall
               //   - otherwise trustedDependencies, as for bun, is required.
               if (!process.env.ZP_IS_SELFENV)
                    zeeltephp_postinstall();
          },
          //configResolved(resolvedConfig) {
          //},
          closeBundle: {
               sequential: true,
               order: 'post',
               async handler() {
                    // Count the closeBundleRuntimes via process.env (string!)
                    //   - while in ZP_IS_SELFENV zp_info is re-initialized at build
                    if (!process.env.ZP_CLOSE_BUNDLE_RUNTIMES)
                         process.env.ZP_CLOSE_BUNDLE_RUNTIMES = 0;
                    process.env.ZP_CLOSE_BUNDLE_RUNTIMES++;
                    if (zp_info.MODE.isBuildMode && process.env.ZP_CLOSE_BUNDLE_RUNTIMES === '2') {
                         zeeltephp_postbuild();
                    }
               }
          }
     };
}

/**
 * Loads and configures ZeeltePHP environment variables (.env)
 * Missing variables are generated from source path /htdocs/<your-svelte-project> 
 * @param {string} state  init | config - loading at top or at config/resolvedConfig
 * @param {string} mode   Vite environment mode (development/production)
 * @returns 
 */
// Utility to ensure required env vars are present and generated if missing
function zeeltephp_loadEnvironment(state, mode) {
     if (state == 'init') {

          // Already loaded ?
          if (process.env?.ZP_ENV_IS_SET) return;
          process.env.ZP_ENV_IS_SET = true;

          // Printer intro / header
          console.log();
          console.log('🐘 ZeeltePHP environment', mode || '');

          // Load the .env config
          if (mode == null && !process.env.ZP_ENV_LOADED) {
               console.log(`   🚨  'mode' is missing or undefined`);
               console.log(`       - if you want your .env config loaded, 'mode' needs to be passed from vite.config.js`);
               console.log(`       - if you have your .env config loaded earlier, add 'ZP_ENV_LOADED=true' to avoid this message.`);
               console.log();
          }
          else if (!process.env.ZP_ENV_LOADED) {
               // -- debug -- console.log('🐘🚀🚨 mode should be zp:', mode);
               // Load the correct .env file for the mode
               zp_info.ENV_LOADED = loadEnv(mode, process.cwd(), '');
               Object.assign(process.env, zp_info.ENV_LOADED); // Merge into process.env for universal access
          }          
     }

     // 2025-05-30 - process.env.NODE_ENV we know if dev|build  (serve|build)
     zp_info.MODE.isDevMode   = process.env.NODE_ENV === 'development';
     zp_info.MODE.isBuildMode = process.env.NODE_ENV === 'production';

     const projectName = path.basename(process.cwd());
     const isDevMode   = zp_info.MODE.isDevMode;
     const buildDir    = process.env.BUILD_DIR || zp_info.ENV_DEFAULTS.BUILD_DIR;
     //console.log('🐘 ZeeltePHP environment', isDevMode, mode || '');

     // Generate and set the defaults at both states init,config
     zp_info.ENV_DEFAULTS = {
          BUILD_DIR: isDevMode ? '' :  buildDir,
          BASE:      isDevMode ? '' : `/${projectName}/${buildDir}`,
          PUBLIC_ZEELTEPHP_BASE: isDevMode
               ? `http://localhost/${projectName}/static/api/`
               : `/${projectName}/${buildDir}/api/`
     };

     // Set defaults and print the .env vars (set if not already present from .env)
     for (const [key, value] of Object.entries(zp_info.ENV_DEFAULTS)) {

          // At init set the current defaults to process.env and if not already present.
          //    - Just in case, so mode dev|build can proceed.
          if (state == 'init' && !process.env[key]) {
               process.env[key] = value;
          }

          // At config - log the present or generated variable to CLI and set to process.env
          //   - if the variable have been loaded earlier, its present in both process.env,ENV_LOADED
          if (state == 'config') {
               if (process.env[key] && zp_info.ENV_LOADED[key]) {
                    console.log(`      ${key} = ${process.env[key]}`);
               }
               else {
                    console.log(`    * ${key} = ${value}`);
                    process.env[key] = value;
               }
          }
     }

     // Print a empty line for nice CLI output after printing the .env vars
     if (state == 'config') console.log();
}

/**
 * Installs required paths and files, if not exists, to consumer-project.
 * @returns void
 */
// replaces previous versions: @/postinstall, @/trustedDependencies, @/zeeltephp-post-install.sh
function zeeltephp_postinstall() {
     try {
          // Prepare tasks
          const tasksTodo = [];
          for (const task of zp_info.POSTINSTALL_TASKS) {
               const cpRoot = process.cwd();
               let realDst  = path.join(cpRoot, task.dst);
               if (!fs.existsSync(realDst))
                    tasksTodo.push(task);
          }

          // Execute the prepared tasks
          if (tasksTodo.length > 0) { 
               console.log('🐘 ZeeltePHP - postinstall ');
               process_tasks(tasksTodo, '');
          }
          
     } catch (error) {
          console.error('🐘❌ postinstall error ')
          throw error; // Ensure install fails on critical errors
     }
}

/**
 * Post-Build Script for ZeeltePHP
 * -------------------------------
 * 1. Copies PHP files to build directory
 * 2. Generates PHP environment file
 * 3. Creates required directories
 */
// replaces @:/package.json/scripts-configs, @:/zp-post-build.sh
function zeeltephp_postbuild() {
     try {
          console.log();
          console.log(`🐘 ZeeltePHP - postbuild`);

          // Set .env file content into task.src 
          //   - hard-coded index from postbuild tasks ENV
          //   - export all BASE, VITE_, PUBLIC_, ZP_, ZEELTEPHP_
          const indexOfTaskEnv = 4;
          zp_info.POSTBUILD_TASKS[indexOfTaskEnv].src = Object.entries(process.env)
               .filter(([key]) => zp_info.ENV_WHITELIST.some(prefix => key.includes(prefix)))
               .map(([key, value]) => `${key}=${value}`) // change - PUBLIC_BASE is just BASE -> key === 'BASE' ? `PUBLIC_BASE=${value}` : 
               .join('\n');

          // Remove first task when ZP_IS_SELFENV
          //   - for the time beeing, let to fail - to check the CLI output for styling and that X works ;-)
          // -- if (process.env.ZP_IS_SELFENV == true) 
          // --     delete zp_info.POSTBUILD_TASKS[0];
          // --     array_shift(zp_info.POSTBUILD_TASKS);

          // Execute tasks
          //   - the BUILD_DIR should be in process.env by now. Should be the same as in zp_info.ENV_CONFIG.BUILD_DIR
          if (process_tasks(zp_info.POSTBUILD_TASKS, process.env.BUILD_DIR, true))
               console.log('🚀 done');
          else console.log('🚨 done with problems');
          console.log();

     } catch (error) {
          console.error('🐘❌ postbuild error ')
          throw error; // Ensure build fails on critical errors
     }
}

/**
 * Executes a list of file tasks which are executed by exec_task()
 * Each task is executed in order. If any task fails, `allSuccess` will be set to false.
 *
 * @param {Array<Object>} tasks - An array of task objects to execute. See exec_task() for attribute details.
 * @param {string} intoDir - The base directory to prepend to each task's `dst` (e.g., your build directory).
 * @param {boolean} [verbose=false] - If true, logs details about each task execution.
 * @returns {boolean} Returns true if all tasks succeeded, false if any failed.
 */
function process_tasks(tasks, intoDir, verbose = false) {
     let allSuccess = true;

     // Execute tasks
     for (const task of tasks) {

          // Set src from zp_info.PATHS if not already set
          if (zp_info.PATHS[task.name])
               task.src = zp_info.PATHS[task.name];
          else task.src = task.src || '';

          // Prepend intoDir to destination
          task.dst = intoDir + task.dst;

          // Execute the task using exec_task
          const result = exec_task(
               task.dst,                // we use dst as name -> name in task is not used as reference to look-up default source path from zp_info.PATHS.
               task.todo,               // the type of task
               task.src    || '',       // source path or .env-file content
               task.dst,                // destination path
               task.filter || (() => true),  // file-filter
               verbose                  // verbose output
          );

          if (!result) 
               allSuccess = false;
     }
     return allSuccess;
}