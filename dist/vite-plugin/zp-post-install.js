// create-static-api.js
import { mkdir, copyFile, cp } from 'node:fs/promises'
import { join, basename } from 'node:path'
import fs from 'node:fs'

//const __dirname = dirname(fileURLToPath(import.meta.url)) // in link mode this is sym-link
//const _consumerRoot = process.cwd(); // Always the consumer's root

export async function zeeltephp_postinstall() {
  try {
    //return // deactivated
    const debug = false;
    
    const packageRoot = process.cwd(); // path to @zeeltephp
    const iSelf = process.env?.VITE_ZP_SELF ? true : false;
    if (iSelf)
        return; // skip self environment - install is only for consumer project

    console.log('🚀 ZeeltePHP - post-install ');

    //const packageName  = process.env.npm_package_name;
    const moduleBase = join(packageRoot, 'node_modules', 'zeeltephp', 'dist');
    const apiBase    = join(moduleBase, 'api')
    const tmplBase   = join(moduleBase, 'templates')

    const consumerRoot = join(packageRoot);
    const destBase     = consumerRoot;

    if (debug) {
      console.log('  -  packageRoot    ', packageRoot);
      //console.log('  -  packageName    ', packageName);
      console.log('     moduleBase     ', moduleBase);
      console.log('     apiBase        ', apiBase);
      console.log('     tmplBase       ', tmplBase);
      console.log('  -  consumerRoot   ', consumerRoot);
      //console.log('     isConsumer     ', isConsumer);
      console.log('     destBase       ', destBase);
      //console.log('  -  Config      ', process.env);
    }

    // 1. Copy ./src/routes/+layout.js file 
    const layoutSource = join(tmplBase, 'src', 'routes', '+layout.js');
    const layoutDest   = join(destBase, 'src', 'routes', '+layout.js');
    if (!fs.existsSync(layoutDest)) {
      console.log('  create /src/routes/+layout.js');
      await copyFile(layoutSource, layoutDest);
    }

    // 2. Copy static/api
    const apiSourcePath = join(tmplBase, 'static', 'api');
    const apiDestPath   = join(destBase, 'static', 'api');
    if (!fs.existsSync(apiDestPath)) {
      console.log('  create /static/api/index.php');
      cp(apiSourcePath, apiDestPath, { recursive: true });
    }

    // 3. Create /src/lib/zplib/
    const zplibDest = join(destBase, 'src', 'lib', 'zplib');
    if (!fs.existsSync(zplibDest)) {
      console.log('  create /lib/zplib');
      await mkdir(zplibDest, { recursive: true })
    }

    // 4. if not exist create /node_modules/zeeltephp/dist/api/log
    const apiLogPathC = join(destBase, 'static', 'api', 'zeeltephp', 'log');
    const apiLogPathM = join(moduleBase, 'api', 'zeeltephp', 'log');
    if (!fs.existsSync(apiLogPathC)) {
      console.log('  create /static/api/zeeltephp/log');
      await mkdir(apiLogPathC, { recursive: true })
    }

    if (!fs.existsSync(apiLogPathM)) {
      console.log('  create @zeeltephp/api/zeeltephp/log');
      await mkdir(apiLogPathM, { recursive: true })
    }

    /*
    if (isFirstInstallTime) {
      // install the zpdemo too
      console.log('  copy /src/routes/zpdemo');
      const demoSourcePath    = join(tmplBase, 'src', 'routes', 'zpdemo');
      const demoDestPath      = join(destBase, 'src', 'routes', 'zpdemo');
      const demoSourcePathApi = join(tmplBase, 'src', 'lib', 'zplib', 'inc.zpdemo.php');
      const demoDestPathApi   = join(destBase, 'src', 'lib', 'zplib', 'inc.zpdemo.php');
      cp(demoSourcePath, demoDestPath, { recursive: true });
      cp(demoSourcePathApi, demoDestPathApi, { recursive: true });
    }
    */
  } catch (err) {
    console.error('❌ Post-install error:')
    console.error('- Message:', err.message)
    console.error('- Code:', err.code || 'N/A')
    console.error('- Path:', err.path || 'N/A')
    process.exit(1)
  }
}
