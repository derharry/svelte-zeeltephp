import { zp_fetch_api } from 'zeeltephp';

export async function load({ params, fetch, url }) {
      try {
            console.log(' >> +page.js/load')
            const res_php = await zp_fetch_api(fetch, url);
            //console.log(res_php);
      
            return {
                  '+page.js': 'Hello from /zpexample/+page.js/load',
                  ...res_php
            }
      } 
      catch (error) {
            console.error(error);
            return {
                  '+page.js': 'error'+error.message
            }
      }

      
}

