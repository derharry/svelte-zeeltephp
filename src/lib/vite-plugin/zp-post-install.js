// create-static-api.js
import { mkdir, copyFile } from 'node:fs/promises'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import fs from 'node:fs'

const __dirname = dirname(fileURLToPath(import.meta.url))


async function zeeltephp_postinstall() {
  try {
    //console.log(' 🚀 ZeeltePHP - post-install ');
    // Path Resolution Fixes
    const tmplBase = join(__dirname, '../templates/')// goto ./dist/templates/
    const destBase = join(__dirname, '../../../../') // goto project-root 
    //console.log('   tmplPath ', tmplBase);
    //console.log('   destPath ', destBase);

    // 1. Copy /src/routes/+layout.js file 
    const layoutSource = join(tmplBase, 'routes/+layout.js')
    const layoutDest = join(destBase, 'src/routes/+layout.js')
    if (!fs.existsSync(layoutDest)) {
      console.log(' 🚀 ZeeltePHP - post-install - create /src/routes/+layout.js');
      await copyFile(layoutSource, layoutDest)
    }

    // 2. Copy static/api/index.php
    const phpSource = join(tmplBase, 'static.api.index.php') 
    const phpDest = join(destBase, 'static/api/index.php')
    if (!fs.existsSync(phpDest)) {
      console.log(' 🚀 ZeeltePHP - post-install - create /static/api/index.php');
      await mkdir(dirname(phpDest), { recursive: true })
      await copyFile(phpSource, phpDest)
    }
    
    // 3. Create /src/lib/zplib/
    const zplibDest = join(destBase, 'src/lib/zplib');
    if (!fs.existsSync(zplibDest)) {
      console.log(' 🚀 ZeeltePHP - post-install - create /lib/zplib', zplibDest);
      await mkdir(zplibDest, { recursive: true })
    }

    // 4. if not exist create /node_modules/zeeltephp/dist/api/log
    if (!fs.existsSync('../api/log')) 
      await mkdir('../api/log', { recursive: true })

  } catch (err) {

    console.error('❌ Post-install error:')
    console.error('- Code:', err.code)
    console.error('- Path:', err.path || 'N/A')
    console.error('- Message:', err.message)
    process.exit(1)

  }
}
zeeltephp_postinstall();
