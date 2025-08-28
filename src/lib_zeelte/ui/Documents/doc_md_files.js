
import { marked } from "marked"
import { scandir, readFile } from 'zeelte'
import { validAnchorID } from 'zeelte'

export function doc_md_collect_files(path, fileSlugName = false) {

     const scandirOpts = {
          recursive:    false,
          collectDirs:  false,
          collectFiles: new RegExp('.md', 'i')
     }
     if (fileSlugName)
          scandirOpts.collectFiles = new RegExp(fileSlugName + '.md', )
     else
          scandirOpts.collectFiles = new RegExp('.md', 'i')

     return scandir(path, scandirOpts)
}


export function doc_md_create_DocToc_menu(mdFilesList = []) {
     const toc = []
     for (const mdFile of mdFilesList) {
          const markup  = readFile(mdFile.path)
          const mdToc   = doc_md_collect_headings(markup)
          const chapter = mdFile.name.replace(/(^\d+[_-])/, '').replace('.md', '')
          toc.push({
               label: chapter,
               route: chapter,
               articles: mdToc
          })
     }
     return toc
}


export function doc_md_custom_marked(markdown) {
     try {
          const newToc     = []
          const renderer   = new marked.Renderer()
          renderer.heading = function (item) {
               const level  = item.depth
               const label  = item.text
               const anchor = validAnchorID(label)
               //toc.push({
               //     label, anchor, level
               //})
               return `<h${level} id="${anchor}">${label}</h${level}>\n`;
          }               
          marked.use({renderer})
          markdown = marked.parse(markdown)
     } catch (error) {
          console.error(error)
          throw error
     }
     return markdown
}


export function doc_md_collect_headings(markdown) {
     const collected = [];
     try {
          const renderer   = new marked.Renderer();
          renderer.heading = function (item) {
               const level  = item.depth
               const label  = item.text
               const anchor = validAnchorID(label)
               collected.push({ label, anchor, level });
               // Return empty string because we only want collected data, not actual rendering here
               return "";
          };
          marked.use({ renderer });
          marked.parse(markdown);
     } catch (error) {
          console.error(error);
          throw error;
     }
     return collected;
}

export function doc_md_generate_toc(markdown) {
     const tocNew = []
     const tocMD  = doc_md_collect_headings(markdown)
     for (const item of tocMD) {
               const chars  = ' '.repeat(item.level)
               const indent = chars == ' ' ? '' : chars
               const anchor = validAnchorID(item.label)
               tocNew.push(`${indent}- [${item.label}](#${anchor})`)
     }
     return tocNew.join("\n")
}