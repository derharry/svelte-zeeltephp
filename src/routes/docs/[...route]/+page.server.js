import path from 'path';
import { readFile, writeFile } from 'zeelte'
import { doc_md_collect_files, doc_md_collect_headings } from 'zeelte/ui/index.js';

const docsDir = path.resolve('src/routes/docs');

function find_md_file(routeSlugName) {
     //const escapedRouteName = routeSlugName.toString().replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // 
     //const regexPattern = routeSlugName + '\\.md$';
     const mdFiles = doc_md_collect_files(docsDir, routeSlugName)
     if (mdFiles && mdFiles.length > 0) {
          const mdFile = mdFiles[0]
          return mdFile.path
     }
     return false
}

/** @type {import('./$types').PageLoad} */
export async function load({ params }) {

     let markdownText = ''
     let markdownToc  = []

     if (params?.route) {
          const mdFile  = find_md_file(params.route)
          markdownText  = readFile(mdFile)
          markdownToc   = doc_md_collect_headings(markdownText)
     }

     return {
          markdown: markdownText,
         // toc: markdownToc
     }
}


/** @satisfies {import('./$types').Actions} */
export const actions = {

	saveChanges: async ({event, request, params}) => {
		const formData = await request.formData();
          const newMarkdown = formData.get('newMarkdown')
          const mdFile      = find_md_file(params.route)
          if (newMarkdown) {
               writeFile(mdFile, newMarkdown)
               const markdownText = readFile(mdFile)
               const markdownToc  = doc_md_collect_headings(markdownText)
               return {
                    markdownText
               }
          }
	}

};