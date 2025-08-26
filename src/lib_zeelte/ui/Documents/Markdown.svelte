<script context="module" module>
  export const ssr = false;
</script>
<script>
     import { browser } from '$app/environment';
     import { marked  } from 'marked';
     import { validAnchorID } from 'zeelte';

     let {
          markup = `
# Topic Example
## 1 Head
### 1.1 Head
### 1.2 Head
#### 1.2.1 Head
## 2 Head
          `,
          toc = $bindable([])
     } = $props()

     let markedMarkup = $state('')

     $effect(() => {
          //console.log('$effect()', markup)
         markedMarkup = markedCustomRendered(markup)
     });

     function markedCustomRendered(markup) {
          //const toc = [];
          //console.log('# markedCustomRendered', markup)
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
          let newMarkup = marked.parse(markup, { async: false, breaks: true, smartypants: true });
          //console.log('/ markedCustomRendered', markup)
          return newMarkup
     }


</script>

<style>
     /* Optional GitHub markdown style */
     /*@import 'github-markdown-css/github-markdown.css';*/
     @import 'https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css';
     .markdown-body {
          box-sizing: border-box;
          width:  100%;
          height: 100%;
          margin:  0 auto;
          padding: 0.5em;
     }
</style>

<pre>{JSON.stringify(toc, null, 1)}</pre>
<div class="markdown-body">{@html markedMarkup}</div>

<!--
<table>
     <tbody>
     <tr>
          <td><pre>{markup}</pre></td>
          <td><pre>{markedMarkup}</pre></td>
          <td>{@html markedMarkup}</td>
          <td></td>
     </tr>
     </tbody>
</table>
-->








<!--


     /**
      * Custom renderer to add id anchors to headings
      * @param markup
      * /
     function markedCustomRendered(markup) {
          if (browser) {
               console.log('#markedCustomRendered()')
               const renderer  = new marked.Renderer()
               markup = marked(markup, { async: false, breaks: true, smartypants: true });
               console.log(markup)
               console.log('/markedCustomRendered()')
               return markup
          }
          /*
          console.log('#markedCustomRendered()')
          const renderer   = new marked.Renderer()
          renderer.heading = function (text, level, raw, slugger) {
               // Slugger, this.slugger not exist
               console.log('  ', 'text   ', text.raw)
               console.log('  ', 'level  ', level)
               console.log('  ', 'raw    ', raw)
               console.log('  ', 'text   ', text)
               /*
               let slug = validAnchor(text.raw);
               console.log('  ', 'slug   ', slug)
               // Prefix with 'h-' if it starts with digit
               //if (/^\d/.test(slug)) slug = 'h-' + slug;
               console.log('   renderer.heading()', level, slug, text)
               //return `<h${level} id="${slug}">${text}</h${level}>\n`;
          };
          //marked.use({ renderer})
          console.log('/markedCustomRendered()')
          return response
          /*** /
     }

     function validAnchor(text) {
          if (!(text instanceof String)) return text
          return text
          .toLowerCase()
          .trim()
          .replace(/[^\w\s-]/g, '') // Remove invalid chars
          .replace(/\s+/g, '-')     // Replace spaces with hyphens
          .replace(/^-+|-+$/g, ''); // Trim hyphens from ends
     }




     /*
     if (typeof window !== 'undefined') {
          htmlMarkedContent = markup
          htmlMarkedContent = markedCustomRendered(markup)
          console.log('markup   ', markup)
          console.log('htmlMarkedContent ', htmlMarkedContent)
     }
	if (typeof window !== 'undefined') {
          /*
		// stash the value...
		const initial = markup;

		// unset it...
		markup = undefined;

		$effect(() => {
               console.log('$effect()', initial)
			// ...and reset after we've mounted
			markup = initial;
               markedMarkup = markedCustomRendered(initial)
		});
          /**/
	}



     /***/

</script>



-->