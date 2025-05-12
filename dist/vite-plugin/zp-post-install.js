// zp-post-install.js
//      issued by zeeltephp_loadEnv()
//      replaces previous versions: @/postinstall, @/trustedDependencies, @/zeeltephp-post-install.sh
import { mkdir, cp } from 'node:fs/promises'
import { join, basename } from 'node:path'
import fs from 'node:fs'
import { console_log_sameline } from './inc.tools.js';

// -- const __dirname = dirname(fileURLToPath(import.meta.url)) // -- in link mode this is sym-link
// -- const _cpRoot = process.cwd(); // Always the consumer's root

/**
 * Installs required paths and files, if not exists, to consumer-project.
 * @returns void
 */
export async function zeeltephp_postinstall() {
  try {
    // flag to use debugging output
    const debug = false;

    // set basic paths
    const cpRoot = process.cwd();                                      // path to /consumer-project-root
    const zpRoot = join(cpRoot, 'node_modules', 'zeeltephp', 'dist');  // path to @zeeltephp-package
    const zpApi  = join(zpRoot, 'api');                                // path to @zeeltephp/api       
    const zpTmpl = join(zpRoot, 'templates');                          // path to @zeeltephp/templates

    if (debug) {
      console.log('🐘 ZeeltePHP - post-install ');
      console.log('  -  cpRoot    ', cpRoot);
      console.log('  -  zpRoot    ', zpRoot);
      console.log('  -  zpApi     ', zpApi);
      console.log('  -  zpTmpl    ', zpTmpl);
      //console.log('  -  Config      ', process.env);
    }

    // 1. copy static/api
    // 2. create /node_modules/zeeltephp/dist/api/log
    // 3. create /node_modules/zeeltephp/dist/api/log
    // 4. create /src/lib/zplib/
    // 5. copy ./src/routes/+layout.js file 
    const copyNpaste_tasks = [
      {
        name: '/static/api',
        src: join(zpTmpl, 'static', 'api'),
        dst: join(cpRoot, 'static', 'api')
      },
      {
        name: '@zeeltephp/api/zeeltephp/log',
        dst: join(zpRoot, 'api', 'zeeltephp', 'log'),
      },
      {
        name: '/static/api/log',
        dst: join(cpRoot, 'static', 'api', 'zeeltephp', 'log'),
      },
      {
        name: '/src/lib/zplib',
        dst: join(cpRoot, 'src', 'lib', 'zplib'),
      },
      {
        name: '/src/routes/+layout.js',
        src: join(zpTmpl, 'src', 'routes', '+layout.js'),
        dst: join(cpRoot, 'src', 'routes', '+layout.js')
      }
    ];

    // process copyNpaste_tasks
    //   create path  = !src & dst 
    //   copy         = src & dst
    for (const task of copyNpaste_tasks) {

      // run task only when path not exists - presuming first time setup
      if (!fs.existsSync(task.dst)) {
        console_log_sameline('🐘  zeeltephp-postinstall, ', task.name);

        // init task-state flag
        //    false  = task unsupported
        //    string = task processed
        task.state = false

        // process task
        if (!task.src && task.dst && !fs.existsSync(task.dst)) {
          // create path
          await mkdir(task.dst, { recursive: true })
          task.state = 'created';
        } else if (task.src && task.dst) {
          // copy
          cp(task.src, task.dst, { recursive: true });
          task.state = 'copied';
        }
        // output state
        console.log('🐘', !task.state ? '❌' : '✅', 'zeeltephp-postinstall', task.state, task.name);
      }
      /*
        -- instead of repeating:
        const create Src  = join(..)
        const create Dest = join(..)
        if (!fs.existsSync(Dest)) {
          await mkdir(Dest, { recursive: true })
          await copyFile(Src, Dest);
          console.log('🚀 ZeeltePHP - created ', DEST);
        }
      */
    }

  } catch (err) {
    console.error('🐘 ❌ zeeltephp-postinstall ERROR ')
    console.error('- Message:', err.message)
    console.error('- Code:', err.code || 'N/A')
    console.error('- Path:', err.path || 'N/A')
    process.exit(1)
  }
}
