// lib_zeelte/lib_ui/Documents/inc.documents.js
//import { scandir } from '../../lib_zeelte/lib_js/io/scandir.js';
//import { scandir } from 'zeelte/io/scandir'
//import { scandir } from 'zeelte/io'
import { scandir, scandir_toTreeview } from 'zeelte';
import { isFile, readJsonFile        } from 'zeelte';




export async function collectDocsMenu(dir) {
     const debug = false;
     let menu_groups = []
     try {
          debug && console.log('#collectDocsMenu()')

          const scan_options = { 
               recursive      : true,
               collectDirs   : new RegExp('^.?\\d+_(.*)\\)?$'), 
               collectFiles  : false,
               debug: false
          }

          const dirs  = await scandir(dir, scan_options)
          const tree  = scandir_toTreeview(dirs)
          //menu_groups = tree

          for (const node of tree) {
               let doc_group = {
                    label: node.name.replace(/(\d+_|\(|\))/g, ''),
                    name:  node.name,
                    pages: []
               }
               if (node.children.length == 0) {
                    const toc_file = node.path + '/page_toc.json'
                    if (isFile(toc_file)) {
                         doc_group.toc   = readJsonFile(toc_file);
                         for (const toc of doc_group.toc) {
                              doc_group.pages.push({
                                   label: toc.label,
                                   href: node.name,
                                   anchor: toc.anchor
                              })
                         }
                    }
               }
               else for (const page of node.children || []) {
                    let page_group = {
                         label: page.name.replace(/(\d+_|\(|\))/g, ''),
                         name:  page.name,
                         href: page.name,
                         toc:   []
                    }
                    const toc_file = page.path + '/page_toc.json'
                    if (isFile(toc_file)) {
                         page_group.toc = readJsonFile(toc_file);
                    }
                    doc_group.pages.push(page_group)
               }
               /***/
               menu_groups.push(doc_group)
          }
          menu_groups.push(tree)
     } catch (error) {
          console.error(error)
     } finally {
          debug && console.log('/collectDocsMenu()')
          return menu_groups
     }
}
