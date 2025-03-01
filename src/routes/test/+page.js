
import { fetch_api } from "$lib/zeeltephp.api";

export async function load({ params, fetch, url }) {
      console.log('load test/+page.js', url)
      const res_php = await fetch_api(fetch, url);
      return {
            res_page: 'hello from /test/+page.js',
            res_php2:  res_php.data.res_php2
      }
}

