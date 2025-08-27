import path from 'path';
import { doc_md_collect_files, doc_md_create_DocToc_menu } from 'zeelte/ui/index.js';

export async function load({}) {

     const docsDir = path.resolve('src/routes/docs');
     const mdFiles = doc_md_collect_files(docsDir)
     const mainToc = doc_md_create_DocToc_menu(mdFiles)

     console.log('mainToc', mainToc)
     return {
          mainToc
     }

}

