import path from 'path';
import { readFile } from 'zeelte'
import { doc_md_collect_files, doc_md_collect_headings } from 'zeelte/ui/index.js';


export async function load({ params }) {

     let markdownText = ''
     let markdownToc  = []

     if (params?.route) {
          const docsDir = path.resolve('src/routes/docs');
          const mdFiles = doc_md_collect_files(docsDir, params.route)
          if (mdFiles && mdFiles.length > 0) {
               const mdFile = mdFiles[0]
               markdownText = readFile(mdFile.path)
               markdownToc  = doc_md_collect_headings
          }
     }

     return {
          markdown: markdownText,
         // toc: markdownToc
     }
}


