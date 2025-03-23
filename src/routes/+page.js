
import { zp_fetch_api } from '$lib/zeeltephp/zp.fetch.api.js';

export async function load({ fetch, url }) {
      try {

            //console.log('load +page.js')
            //console.log('load +page.js', {url}, {fetch})
            const res_php_home = await zp_fetch_api(fetch, url);
            //console.log(' @@ ', res_php_home)

            return {
                  res_load: 'Hello from /+page.js',
                  res_php : res_php_home || 'fallback in +page.js -> error at php'
            }
      }
      catch (error) {
            return {
                  res_load: 'Hello from /+page.js',
                  res_php :  error,
            }
      }
}

