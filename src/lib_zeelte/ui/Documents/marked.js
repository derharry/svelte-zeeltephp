import { marked } from "marked"
import { validAnchorID } from 'zeelte'

export function customMarked(markdown) {
     try {
          const newToc     = []
          const renderer   = new marked.Renderer()
          renderer.heading = function (item) {
               //let slug = validAnchor(text.raw);
               const level  = item.depth
               const label  = item.text
               const anchor = validAnchorID(label)
               //toc.push({
               //     label, anchor, level
               //})
               return `<h${level} id="${anchor}">${label}</h${level}>\n`;
               /***/
               //return item
          }               
          marked.use({renderer})
          markdown = marked.parse(markdown)
     } catch (error) {
          console.error(error)
          throw error
     }
     return markdown
}