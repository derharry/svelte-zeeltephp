import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
import { json } from "@sveltejs/kit";




export class ZP_ApiRouter 
{

      route     = null
      method    = 'GET'
      action    = null
      value     = null
      params    = []
      data      = null

      url       = PUBLIC_ZEELTEPHP_BASE;


      dataIsInstanceOfFormData = false
      fetch_url = null
      fetch_options = {}
      /*
      fetch_data    = {
            zp: {
                  route: null,
                  method: this.method,
                  action: this.action,
                  value:  this.value,
                  
            },
            body: ... this.data
      }
      */
      fetch_headers = {}
      body          = null


      constructor(url, data = undefined , method = undefined) {
            this.route = url.pathname
            this.url   = PUBLIC_ZEELTEPHP_BASE;
            this.fetch_url   = PUBLIC_ZEELTEPHP_BASE;

      }

}


