// create-static-api.js
import { mkdir, copyFile, cp } from 'node:fs/promises'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import fs from 'node:fs'

//const __dirname = dirname(fileURLToPath(import.meta.url)) // in link mode this is sym-link

const _consumerRoot = process.cwd(); // Always the consumer's root

async function zeeltephp_postinstall() {
  try {
    console.log('🚀 ZeeltePHP - post-install ');

    // only run when in consumer ./_node_modules/  !lib-project-itself
    if (dirname(_consumerRoot) == 'svelte-zeeltephp' || !_consumerRoot.includes('node_modules')) {
      throw new Error('ZeeltePHP - post-install is only for consumer projects!')
    }

    const moduleBase   = join(_consumerRoot, 'dist');
    const consumerBase = join(_consumerRoot, '..', '..');
    const apiBase  = join(moduleBase, 'api')      // goto ./dist/templates/
    const tmplBase = join(moduleBase, 'templates')// goto ./dist/templates/
    const destBase = consumerBase     // goto project-root 

    console.log('  -  moduleBase    ', moduleBase);
    console.log('  -  consumerBase  ', consumerBase);
    console.log('  -  apiBase       ', apiBase);
    console.log('  -  tmplBase      ', tmplBase);
    console.log('  -  destBase      ', destBase);
    //console.log('  -  __dirname ', __dirname);

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
  } catch (err) {

    console.error('❌ Post-install error:')
    console.error('- Code:', err.code)
    console.error('- Path:', err.path || 'N/A')
    console.error('- Message:', err.message)
    process.exit(1)

  }
}
zeeltephp_postinstall();
