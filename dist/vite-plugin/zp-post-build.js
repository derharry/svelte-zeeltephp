// create-static-api.js
import { mkdir, copyFile } from 'node:fs/promises'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import fs from 'node:fs'

const __dirname = dirname(fileURLToPath(import.meta.url))


async function zeeltephp_postinstall() {
  try {
    console.log(' 🚀 ZeeltePHP - post-install ');

    // Path Resolution Fixes
    const tmplBase = join(__dirname, '../templates/')// goto ./dist/templates/
    const destBase = join(__dirname, '../../../../') // goto project-root 
    //console.log('   tmplPath ', tmplBase);
    //console.log('   destPath ', destBase);

    // 1. Copy static/api/index.php
    const phpSource = join(tmplBase, 'static.api.index.php') 
    const phpDest = join(destBase, 'static/api/index.php')
    console.log('   copying static/api/index.php')
    console.log('   from:', phpSource)
    console.log('   to:', phpDest)
    if (!fs.existsSync(phpDest)) {
      console.log(' 🚀 ZeeltePHP - post-install - install /static/api/index.php');
      await mkdir(dirname(phpDest), { recursive: true })
      await copyFile(phpSource, phpDest)
    }

    // 2. Copy /src/routes/+layout.js file 
    const layoutSource = join(tmplBase, '+layout.js')
    const layoutDest = join(destBase, 'src/routes/+layout.js')
    if (!fs.existsSync(layoutDest)) {
      console.log(' 🚀 ZeeltePHP - post-install - install /src/routes/+layout.js');
      await copyFile(layoutSource, layoutDest)
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
