
import { zp_fetch_api } from "$lib/zeeltephp/zeeltephp.api";


export async function load({ params, fetch, url }) {
      //console.log('load test/+page.js', url)
      const res_php = await zp_fetch_api(fetch, url);
      //console.log(res_php);
      return {
            res_page: 'hello from /test/+page.js',
            ...res_php.data
      }
}

