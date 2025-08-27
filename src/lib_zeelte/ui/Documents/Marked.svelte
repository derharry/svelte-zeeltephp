<script>

     import { doc_md_custom_marked } from 'zeelte'

     let {
           markdown  = '',
           toc       = null
     } = $props()

     const markdown_example = `
# Topic Example
## 1 Head
### 1.1 Head
### 1.2 Head
#### 1.2.1 Head
## 2 Head
     `

</script>


<div class="markdown">{@html doc_md_custom_marked(markdown)}</div>


<style>
     
     /* GitHub markdown style */
     /*@import 'github-markdown-css/github-markdown.css';*/
     @import 'https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css';
     
     .markdown {
          box-sizing: border-box;
          display: block;
          width:  100%;
          height: 100%;
          margin:  0;
          padding: 0.5em;
          overflow: auto;
     }

</style>
