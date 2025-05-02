// create-static-api.js
import { mkdir, copyFile } from 'node:fs/promises'
import { dirname } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))


async function zeeltephp_postinstall() {
  try {

    console.log(' 🚀 ZeeltePHP - post-install ');

    const distPath = __dirname; 
    const destPath = `${__dirname}/../../../../`
    console.log('   srcDir  ', distPath);
    console.log('   destDir ', destPath);

    // /src/static/api/index.php
    await mkdir(destPath+'static/api', { recursive: true })
    await copyFile(distPath+'templates/static.api.index.php', destPath+'static/api/index.php');

    // /src/routes/+layout.js 
    await copyFile(distPath+'templates/+layout.js', destPath+'src/routes/+layout.js');

  } catch (error) {
    if (err.code !== 'EEXIST') throw err
  }
}
zeeltephp_postinstall();
/*

async function createStaticApiFolder() {
  const folderPath = `${__dirname}/static/api`
  console.log('Creating static/api directory' , folderPath)
  
  try {
    await mkdir(folderPath, { recursive: true })
    console.log('Created static/api directory')
  } catch (err) {
    if (err.code !== 'EEXIST') throw err
  }
}

createStaticApiFolder()
*/