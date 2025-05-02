// create-static-api.js
import { mkdir } from 'node:fs/promises'
import { dirname } from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = dirname(fileURLToPath(import.meta.url))

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
