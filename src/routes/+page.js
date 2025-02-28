
import { fetch_api } from "$lib/zeeltephp.api";

export async function load({ params, fetch, url }) {
      //console.log('load +page.js')
      const res_php = await fetch_api(fetch);
      return {
            res_page: 'hello from +page.js',
            res_php:  res_php.data.res_php
      }
}

