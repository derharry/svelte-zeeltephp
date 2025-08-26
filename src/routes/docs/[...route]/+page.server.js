import { findRouteMDFile } from './shared.js'
import { readFile } from 'zeelte'


export async function load({ params }) {

     let markdownText = undefined

     if (params?.route) {
          const route = params.route
          const file  = findRouteMDFile(route)
          if (file?.name) {
               markdownText = readFile(file.path)
          }
     } 

     return {
          markdown: markdownText
     }
}

