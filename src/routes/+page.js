
import { fetch_api } from "$lib/zeeltephp.api";

export async function load({ params, fetch, url }) {
      console.log('load +page.js')
      const res_php1 = await fetch_api(fetch);
      return {
            res_page: 'hello from +page.js',
            res_php1:  res_php1.data.res_php1
      }
}

