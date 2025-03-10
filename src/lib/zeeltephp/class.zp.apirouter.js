// class.zp.api.router.php
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { base } from "$app/paths";
import { json } from "@sveltejs/kit";

export class ZP_ApiRouter 
{

      // ZP_ApiRouter Svelte + PHP
      route     = null
      action    = null
      value     = null
      params    = []
      data      = null

      // Svelte CSR specific (-> PHP specific)
      environment = 'dev | build';
      method      = 'GET'
      zp_baseUrl  = PUBLIC_ZEELTEPHP_BASE;

      // CSR specfic -> zp_fetch_api() 
      fetch_url     = null
      fetch_options = {
            'method' :  this.method,
            'headers':  {},
            'body'   :  this.fetch_data
      }
      fetch_data    = {
            zp: {
                  route: null,
                  method: this.method,
                  action: this.action,
                  value:  this.value,
                  body_data: this.data
            }
      }
      dataIsInstanceOfFormData = false



      constructor(url, data = undefined , method = undefined) {
            this.route = url.pathname
            this.url   = PUBLIC_ZEELTEPHP_BASE;
            this.fetch_url   = PUBLIC_ZEELTEPHP_BASE;

      }

}


