
import { fetch_api } from "$lib/zeeltephp.api";

export async function load({ params, fetch, url }) {
      console.log('load +page.js')
      const res_php_home = await fetch_api(fetch, url);
      return {
            res_page: 'hello from /+page.js',
            res_php1:  res_php_home.data.res_php,
            res_php2:  res_php_home.data.res_phpstatic,
      }
}

