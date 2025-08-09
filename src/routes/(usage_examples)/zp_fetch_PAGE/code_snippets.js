export const code_colors = {
     javascript: {
          fallbackColor: 'red',
          words: [ 'props' ]
     },
     php: {
          fallbackColor: 'blue',
          words: [ '#\{(/.*/)\}#', '#from .(.*)$#' ]
     }
};

export const code_snippets = [
{
     title:         `props <> ZeeltePHP stores `,
     description:   `Which origin is from $page.data, $page.form, $page.error
                     
                     Withe ZeeltePHP this is equivalent to..
     `,
     svelteKit4:    `export let data
                     export let form
                     export let error
     `, 
     svelteKit5:    `let {
                         data,
                         form,
                         error,
                     } = $props()
     `,
     zeelteJS:     `import { data, form, error } from 'zeelte.php';`
},
{
     title:         `fetch <> zp_fetch()`,
     description:   'description',
     svelteKit4:    `fetch(url, options) 
     `, 
     svelteKit5:    `fetch(url, options) 
     `,
     zeelteJS:     `import { zp_fetch() } from 'zeelte.php';
                    zp_fetch(url, options)
     `,
     zeeltePHP:    `<?php
                    ?>
     `
},
{
     title:         `port zp_fetch_api() to zp_fetch()`,
     description:   'description',
     svelteKit4:    `zp_fetch_api(router, data, method, headers, debug) `, 
     zeelteJS:      `zp_fetch(url, options = {
                         data,
                         method,
                         headers,
                         debug
                     })`,
}
];