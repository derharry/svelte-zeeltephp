
import { zp_fetch_api } from '$lib/zeeltephp/zp.fetch.api.js';

export async function load({ fetch, url }) {
      try {

            //console.log('load +page.js', {url}, {fetch})
            const res_php_home = await zp_fetch_api(fetch, url);

            return {
                  res_load: 'Hello from /+page.js',
                  res_php : res_php_home.data // 'dummy.php' //
            }
      }
      catch (error) {
            console.error({error});
      }
}

