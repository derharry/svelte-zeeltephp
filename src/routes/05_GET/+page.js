
import { zp_fetch_api } from 'zeeltephp';
import { ZP_ApiRouter } from "zeeltephp";
import { ZP_EventDetails } from 'zeeltephp';


export async function load({ params, fetch, url }) {
      try {
            //clone CSR/Svelte Part
            const csrED = new ZP_EventDetails( new URL(url) );
            const csrAR = new ZP_ApiRouter(    new URL(url) );

            // we only want to use method GET - override
            //csrAR.method = 'GET';
            // pass override via param ?o=GET :-)
            if (csrAR.data?.o) {
                  console.log('MATCH o', csrAR.data.o)
                  csrAR.method = csrAR.data.o
                  csrAR.prepare();
            }

            let phpAR = null;
            phpAR = await zp_fetch_api(fetch, csrAR);

            return  {
                  '+page.js': {
                        message: '+page.js/data/zpAR',
                        csrAR,
                        csrED,
                  },
                  '+page.server.php': {
                        ...phpAR
                  }
                  
            }
      }
      catch (error) {
            return {
                  error: error.message
            }
      }
}

