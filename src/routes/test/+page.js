
import { fetch_api } from "$lib/zeeltephp.api";

export async function load({ params, fetch, url }) {
      console.log('load +page.js')
      const res_php2 = await fetch_api(fetch);
      return {
            res_page: 'hello from /test/+page.js',
            res_php2:  res_php2.data.res_php2
      }
}

