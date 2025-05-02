//import { zp_fetch_api } from '../../../../index.js';
import { zp_fetch_api } from 'zeeltephp';


export async function load({ params, fetch, url }) {
      try {
            let res_php = { // fixture
                  res_pageserver : 'hi 1',
                  res_phplib     : 'hi 2',
                  res_phplibsub  : 'hi 3',
                  res_phplibsub  : 'hi 3',
                  res_phpzplib   : 'hi 4'
      
            }
            console.log('load test/+page.js', url)
            res_php = await zp_fetch_api(fetch, url);
            console.log(res_php);
      
            return {
                  '+page.js': 'Hello from /01_basics/+page.js',
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

