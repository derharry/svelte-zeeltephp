
import { zp_fetch_api } from "$lib/zeeltephp/zeeltephp.api";

export async function load({ fetch, url }) {
      try {
            console.log('load +page.js', {url}, {fetch})
            const res_php_home = await zp_fetch_api(fetch, url);
            return {
                  res_page: 'hello from /+page.js',
                  res_php1:  res_php_home.data.res_php,
                  res_php2:  res_php_home.data.res_phpstatic,
            }
      }
      catch (error) {
            console.error({error});
      }
}

