import { readFile, writeFile } from 'zeelte'
import path from 'path';
import { doc_md_collect_files, doc_md_create_DocToc_menu, doc_md_generate_toc } from 'zeelte/ui/index.js';

export function load({}) {

     const docsDir = path.resolve('src/routes/docs');
     const mdFiles = doc_md_collect_files(docsDir)
     const mainToc = doc_md_create_DocToc_menu(mdFiles)
     
     /*
     let mainTocStr = ''
     for (const toc of mdFiles) {
          const markdownText = readFile(toc.path)
          mainTocStr += doc_md_generate_toc(markdownText)+"\n"
     }
     writeFile(docsDir+'/test2.txt', mainTocStr)
     /***/
     return {
          mainToc
     }
}