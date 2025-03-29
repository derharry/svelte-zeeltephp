
import { zp_fetch_api } from '$lib/zeeltephp/zp.fetch.api.js';
import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
import { page } from '$app/state';

export async function load({ params, fetch, url }) {
      try {

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

