import { scandir, readFile } from 'zeelte'
import path from 'path';

export async function load({}) {

     const docsDir = path.resolve('src/routes/docs');
     const content = scandir(docsDir, {
          recursive:    true,
          collectDirs:  false,
          collectFiles: new RegExp('.toc.json')
     })

     const mainToc = []
     for (const item of content) {
          const file = readFile(item.path)
          if (file) {
               const json = JSON.parse(file)
               mainToc.push(...json)
          }
     }
     
     return {
          mainToc
     }
}
