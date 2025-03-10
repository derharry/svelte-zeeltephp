
import { zp_fetch_api } from "$lib/zeeltephp/zeeltephp.api";


export async function load({ params, fetch, url }) {
      console.log('load test/+page.js', url)
      const res_php2 = await zp_fetch_api(fetch, url);
      return {
            res_page: 'hello from /test/+page.js',
            res_php1:  res_php2.data.res_php,
            res_php2:  res_php2.data.res_phpstatic,
            res_php3:  res_php2.data.res_phpzplib,
      }
}

