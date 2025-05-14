// class.zp.api.router.php
import { dev }  from "$app/environment";
import { page } from '$app/state';
import { ZP_EventDetails } from "./class.zp.eventdetails.js" // -> import { ZP_EventDetails } from "../../../zeeltephp"
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { tinyid } from './tiny.id.js';
import { zp_page_route } from "./inc.zp.tools.js";
//let PUBLIC_ZEELTEPHP_BASE = '';

/**
 * Api/Router - Svelte <-> ZeeltePHP
 * See constructor for overloading
 */
export class ZP_ApiRouter 
{

      // ZP_ApiRouter Svelte + PHP
      route     = null  // zp_route
      action    = null  // zp_action
      value     = null  // zp_value
      params    = null  // zp_params - fetch_query
      data      = null  // zp_data   - any (formData | JSON | ..)

      // Svelte CSR specific (-> PHP specific)
      environment = 'dev | build';  // internal flag to know dev (./src/*, .node_modules/[roadmap], ..) or build (zeeltephp: ./api/)
      method      = 'GET'           // Best method to send or pars urlRouteString

      // CSR specfic -> zp_fetch_api() 
      base_url       = PUBLIC_ZEELTEPHP_BASE;
      fetch_url      = null // final url   to use at zp_fetch_api(fetch_url, fetchOptions)
      fetch_query    = null // final query that holds the part ?.. as query_string
      fetch_options  = null // final options to use at zp_fetch_api(fetch_url, fetchOptions)
      fetch_enctype  = 'application/json'
      dataIsFormData = false // flag for best method to just 'POST' and use default form-enctype else fallback to JSON-data
      message        = ""; // deprecated to use log and debug_msgs // message for debugging

      // internal
      runtimeID  = '';
      debug      = false;
      debug_msgs = [];
      log(msg, value = '') {
            if (this.debug) {
                  this.debug_msgs.push({msg, value});
                  console.log('  ', msg, value);
            }
      }

      /**
       * Create the router with default settings and auto-detection.
       * Overloading:
       * ZP_ApiRouter()                    
       *    ready-to-fetch, most suitable to use in +page.svelte
       * ZP_ApiRouter(router)
       *    Any               Support auto-detect most use-cases.
       *    ZP_EventDetails   As ZP_EventDetails supports auto-detection.
       *    ZP_ApiRouter      me-self
       *    String            future +server.php, +api.php
       * @param {*} router Any, ZP_ApiRouter, String, ZP_EventDetails
       * @param {*} data   force data to send. overrules auto detected from ZP_EventDetails
       * @param {string} method force using method GET, POST, PUT, ...
       * @param {*} debug  active and show debug info
       * @returns ZP_ApiRouter
       */
      //constructor(router, data = undefined , method = undefined) {
      constructor(router = undefined, data = undefined, method = undefined, debug = false) {
            try {
                  // exit first
                  //   if typeof( event) == myself - return myself
                  // -- if is_nullish( router ) - exit false
                  //    do we really exit? or do we defaults to all to page/load() ?
                  //if (router == undefined || router == null) return false; 
                  if (router instanceof ZP_ApiRouter) return router;

                  // debugging?
                  this.debug = debug; // for now because @debug is no param
                  if (debug) console.log('-- ZP ApiRouter ');

                  // main()
                  if (debug) this.runtimeID = tinyid(); // avoid overhead, only dev mode
                  if (debug) this.log('rtid', this.runtimeID);

                  // running mode dev or prod 
                  this.environment = dev ? 'dev' : 'prod';
                  if (debug) this.log('env', this.environment);

                  // fetch routing:
                  // first fetch from current page
                  this.parse_routingFromSveltePage();

                  // overloading()
                  // 2025-05-11 to avoid multiple times ZP_EventDetails
                  //    lets do not .. but because it we be done at else as fallback
                  // -- if (router instanceof URL && router?.pathname) { .. } 
                  
                  if (typeof router == 'string') {
                        // decode string ? for dev purposes?
                        // usage of +api.php or like..
                        if (router.startsWith('+')) {
                              // v1.0.1 - added + to route to /src/routes/api/**
                              this.message = 'ZP_ApiRouter-api/**'
                              this.route   = router;
                        } else {
                              this.message = 'ZP_ApiRouter-string'
                              this.parse_url_string(router)
                        }
                  } else {                        
                        // fallback event details
                        //   ZP_EventDetails knows most use-cases
                        //       SubmitEvent, PointerEvent, etc etc
                        let check = false; // as soon true, no furthers checks required.
                        if (!check) {
                              // lets check if router can be fetched by ZP_EventDetails
                              this.log('  zpED from router')
                              check = this.parse_routingFromZPeventDetails(router);
                        }

                        /*
                        if (!check) {
                              this.log('  zpED from data')
                              check = this.parse_routingFromZPeventDetails(data);
                              // the data is probably in ed.data ;-)
                              data = data || check.data
                        } else
                              this.set_data(data);
                        */
                        //check = this.parse_routingFromZPeventDetails(router);
                        // still not ?
                        if (!check) {
                              this.log('  ! unsupported router') 
                        }
                  }
                  
                  //// finalize zp_route ??
                  //   ensure zp_route is /route/    starting,ending with /
                  this.route = '/'+this.route+'/';
                  this.route = this.route.replace('//', '/');

                  // finalize
                  this.log('finalize');
                  this.set_data(data);
                  this.set_best_method(method);
                  // -- this.prepare(); // is called in set_best_method();
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            } finally {
                  this.log(this);
                  if (debug) console.log('// ZP ApiRouter ');
            }
      }

      dump() { 
            console.log('---DUMP ZP_ApiRouter-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
                if (value !== undefined && value !== null) 
                    console.log(variable, value);
                
            });
            console.log('---END DUMP ZP_ApiRouter-----------------------------');
      }


      /**
       * Parse Svelte/$app/page.
       * ZPPHP destructs this as ?/route/&?/action=value&query_params
       */
      parse_routingFromSveltePage() {
            //this.log('parse_routingFromSveltePage');
            //this.route = zp_page_route();
            this.log('zpR routingSveltePage ', this.route);
            // deparse fetch of current routing 
            // this.parse_routingFromZPeventDetails(page.url);
            // we should be set now
            // set default current route
            // -- this.route = zp_page_route();
            // -- this.log('zp_page', this.route);
            // -- zp_page_route(true);
      }

      parse_routingFromZPeventDetails(event) {
            this.log('parse_routingFromZPeventDetails');
            const zpED = new ZP_EventDetails(event, this.debug);
            if (zpED) {
                  this.action = zpED.action || this.action;
                  this.value  = zpED.value || this.value;
                  this.route  = zpED.route || this.route;
                  this.set_data(zpED.data);
                  // data ??
                  //this.route  = this.routezp || zpED.route;
                  //this.action = zpED.action || this.action;
                  //this.value  = zpED.value || this.value;
                  // -- ??? this.fetch_query = page.url.search
                  // -- ??? this.fetch_enctype = zpED.encoding || this.fetch_enctype
                  // -- ??? this.data   = page.params; // wherefor?
                  this.fetch_query = page.url.search || this.fetch_query;
                  this.log('zpR zpEventDetails ', this.route);
                  return zpED;
            }
            this.log(' -- zpR', this.route);
            return false;
      }


      set_data(data) {
            if (!data) return;
            this.log('set_data')
            this.data = data
            this.dataIsFormData = data instanceof FormData ? true : false;
      }


      /**
       *
       * @param {string} forceMethod force / lock method beeing used
       */
      set_best_method(forceMethod = undefined) {
            let isSet = '-' // string-cli-log
            if (forceMethod != undefined) {
                  isSet = 'forced'
                  this.method = forceMethod
            }
            else if (this.action) {
                  isSet = 'formData'
                  this.method = 'POST'
            }
            else if (this.data) {
                  isSet = 'json'
                  this.method = 'POST'
            } else {
                  isSet = 'auto'
                  this.method = 'GET';
            }
            this.log(`set_best_method(', ${this.method}, ${isSet}, ')`)
            this.prepare(); // update ready-to-fetch-state
      }
      

      /**
       * ready-to-fetch state.
       */
      prepare() {
            this.log('prepare')
            // prepare to be in a ready-to-go for fetch state a la @zeelte/api //--IZP_ApiRouter(Svelte<>Zeelte_PHP)
            // supported
            //    GET  = URL :string      = PUBLIC_ZEELTEPHP_BASE?/route/&?/action=value&any
            //                ?/route/        : required so ZeeltePHP can find route/+page.server.php 
            //                ?/action=value  : optional action from formAction or override, value : optional
            //                &any_params_data: this.params or just this.fetch_query
            //                                  this.data data is not supported or we push key_values into the get :-) 
            //                                            or JSON stringify?
            //    POST = ZP_ApiRouter :JSON 
            //                zp_route  = ?/route/
            //                zp_action = ?/action
            //                zp_value  = =value
            //                zp_data   = &any
            //  PUT, PATCH, ... for roadmap ideas
            
            // -- first set defaults
            // -- do not set fetch_url here - to allow override and reuse prepare()
            // -- is set at class definition -> base_url = PUBLIC_ZEELTEPHP_BASE; or override
            // -- do not set best method here - to allow override and reuse prepare()
            // -- is done at constructor only once -> this.set_best_method(method);
            if      (this.method == 'GET')  this.prepare_GET()
            else if (this.method == 'POST') this.prepare_POST();
            else     this.log(' - unsupported RequestType');
      }


      prepare_GET() {
            this.log('prepare_GET')
            // we now only need to define the full fetch_url and empty fetch_options
            // create URL-query-string interface ZPI_ApiRouter
            // set the final fetch_url
            // this.fetch_url = this.base_url + this.fetch_query
            const zpURL = []
            const zpURLPush = (data = null) => {
                  if (data !== null && data !== undefined) {
                  if (typeof data == 'array') {
                        zpURL.concat(data)
                  }
                  else if (typeof data == 'object') {
                        for (const [key, value] of Object.entries(data)) {
                              let param = key
                              if (page && value) param += '=' + value
                              zpURL.push(param)
                        }
                  } else {
                        zpURL.push(data)
                  }
            }}

            // add zp_route
            zpURLPush(this.route);   
            // add zp_action
            let actionStr = this.action
            if (actionStr && this.value) actionStr += '=' + this.value
            zpURLPush(actionStr);  
            // params is already pushed to data! and ignored from now params = depricated. -> zpURLPush(this.params);
            zpURLPush(this.data);

            // set baseUrl + zp_route/&
            this.fetch_url = this.base_url +'?'+ zpURL.join('&');
            this.fetch_options = {};
      }

      prepare_POST() {
            this.log('prepare_POST');

            // PREPARE final for fetch(ZeeltePHP)
            this.message = 'prepare( ZP_ApiRouter(Svelte<->ZeeltePHP :POST))' 
            
            // prepare data
            let data = null;
            if (this.data instanceof FormData) {
                  // don't set 'content-type' - it works out of the box :-O this.fetch_options.headers['Content-Type'] = 'multipart/form-data';
                  // -> doesn't matter where the ZP_vars are defined -> as long it is in $_POST
                  data = this.data 
                  data.append('zp_route', this.route)
                  data.append('zp_action', this.action)
                  data.append('zp_value', this.value)
                  this.message = 'prepare: POST/FormData'
            } else {
                  // fallback to default content-type json
                  // this.fetch_options.headers = {
                  //      'Content-Type': 'application/json; charset=UTF-8'
                  // }
                  data = JSON.stringify({
                        'zp_route' : this.route,
                        'zp_action': this.action,
                        'zp_value' : this.value,
                        'zp_data'  : this.data
                  });
                  this.message = 'prepare: POST/JSON'
            }

            // set baseUrl + zp_route/&
            this.fetch_url     = this.base_url;
            this.fetch_options = {
                  method:     this.method,
                  headers:    {
                        //'Content-Type': this.fetch_enctype
                  },
                  body: data // data
            }
      }

}



/**
      /**
       * Destruct the given URL:string to be reused 
       * renamed from parse_url_string and maybe deprecated.
       * intended to be a destructor to check custom url:string .
       * @param {*} urlString 
       * /
      decode_url_string(urlString) {
            let debug = false;
            try {
                  if (debug) console.log(' () ', urlString);

                  let [first,   second, ...third] = urlString.split('&')
                  let [baseUrl, route,  action, ...params] = first.split('?')
                  
                  if (debug) console.log('1) ', {first}, {second}, {third})
                  if (debug) console.log('2) ', {baseUrl}, {route}, {action}, {params})

                  // set url 
                  this.base_url = baseUrl || this.base_url;

                  const parse = (variable) => {
                        if (!variable) return false
                        let response = {
                              route  : null,
                              action : null, 
                              value  : null,
                              params : []
                        }
                        if (variable.startsWith('?/') || variable.startsWith('/')) {
                              const [action, value] = variable.split('=')
                              response.action = action.startsWith('?') ? action : '?'+ action
                              response.value  = value
                        }
                        else if (variable.includes('='))
                              response.params.push(variable)
                        else  response.route = variable
                        //return response
                        //}
                        //const parseResponse = (response) => {
                        if (response.route && !this.route) this.route = response.route
                        //else console.error('route already set', this.route, response.route)
                        if (response.action && !this.action) this.action = response.action
                        //else console.error('action already set')
                        if (response.value && !this.value) this.value = response.value
                        // else console.error('value already set')
                        if (response.params?.length > 0) this.params = this.params.concat(response.params)
                  }
                  parse(action) //parseResponse(parse(action))
                  parse(route)  //parseResponse(parse(route))
                  parse(second) //parseResponse(parse(second))
                  if (params.length > 0) this.params = this.params.concat(params)
                  if (third.length  > 0) this.params = this.params.concat(third)
            } catch (error) {
                  this.message = error;
                  console.error({ error });
            }
      }


 */
