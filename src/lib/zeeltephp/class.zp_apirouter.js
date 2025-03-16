
import { base } from "$app/paths";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";

export class ZP_ApiRouter {

      // ZP_ApiRouter Svelte + PHP
      route     = null
      action    = null
      value     = null
      params    = null
      data      = null

      // Svelte CSR specific (-> PHP specific)
      environment = 'dev | build';
      method      = 'GET'
      zp_baseUrl  = PUBLIC_ZEELTEPHP_BASE;

      // CSR specfic -> zp_fetch_api() 
      fetch_url      = null
      fetch_query    = null
      fetch_options  = null
      dataIsFormData = false
      message        = null



}