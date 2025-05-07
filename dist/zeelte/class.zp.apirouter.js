// class.zp.api.router.php
import { dev }  from "$app/environment";
import { page } from '$app/state';
import { ZP_EventDetails } from "./class.zp.eventdetails.js" // -> import { ZP_EventDetails } from "../../../zeeltephp"
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
//let PUBLIC_ZEELTEPHP_BASE = '';

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
      message        = null // message for debugging

      /**
       * 
       * @param {*} routeUrl = string | event | submitter?form-send/ 
       * @param {*} data     = any
       * @param {*} method   = string:GET, POST, PUT, ... 
       */
      //constructor(routeUrl, data = undefined , method = undefined) {
      constructor(routeUrl = undefined, data = undefined, method = undefined) {
            try {
                  // if instance of self - then just return self :-)
                  if (routeUrl instanceof ZP_ApiRouter) return routeUrl;
                  this.environment = dev ? 'dev' : 'build'

                  // fetch routing:
                  // first fetch from current page
                  this.parse_routingFromSveltePage();
                  
                  // and now parse_routeURL() _
                  if (routeUrl instanceof URL && routeUrl?.pathname) {
                        const ed = new ZP_EventDetails(routeUrl)
                        this.route   = ed.route
                        this.action  = ed.action
                        this.value   = ed.value
                        this.data    = ed.data
                        this.fetch_query = routeUrl.search
                        //this.params  = routeUrl.searchParams
                        this.message = 'ZP_ApiRouter-URL'
                  }
                  else if (typeof routeUrl == 'string') {
                        if (routeUrl.startsWith('+')) {
                              // v1.0.1 - added + to route to /src/routes/api/**
                              this.message = 'ZP_ApiRouter-api/**'
                              this.route   = routeUrl;
                        } else {
                              this.message = 'ZP_ApiRouter-string'
                              this.parse_url_string(routeUrl)
                        }
                  }
                  else {

                        let ed = data instanceof ZP_EventDetails ? data : new ZP_EventDetails(routeUrl);
                        if (ed && ed?.route) {
                              this.route   = ed.route
                              this.action  = ed.action
                              this.value   = ed.value
                              data = data || ed.data
                              this.message = ed.message
                        } 
                        else 
                              this.message = 'unknown routeURL'
                        // routeUrl = undefined 
                        // 
                        // nothing to do routeUrl :-) 
                  }
                  this.route = this.route.replace(/^\//, '', this.route)
                  this.route = this.route.replace(/\/$/, '', this.route)
                  this.route = '/'+this.route+'/';

                  //this.message = 'ZP_ApiRouter-defaults'
                  // but before set further defaults check if param/data is ZP_EventDetails
                  //if (routeUrl instanceof SubmitEvent || routeUrl instanceof PointerEvent || ... ) { .. }
                  /*
                  let ed = data instanceof ZP_EventDetails ? data : new ZP_EventDetails(routeUrl);
                  if (ed && ed?.route) {
                        this.message = 'ZP_ApiRouter-else-ZP_EventDetails';
                        this.route   = ed.route;
                        this.action  = ed.action;
                        this.value   = ed.value;
                        this.fetch_enctype = ed.encoding || this.fetch_enctype
                        data = data || ed.data
                        this.message = this.message +'-message:'+ ed.message;
                  }
                  */
                  this.set_data(data);
                  this.set_best_method(data, method);
                  this.prepare();
            } catch (error) {
                  this.message = error;
                  console.error({ error })
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
            try {
                  // set default current route 
                  // use ZP_EventDetails to get the details :-)
                  this.route  = page.url.pathname+'/';
                  const ed = new ZP_EventDetails(page.url.searchParams);
                  //this.route  = ed.route;
                  this.action = ed.action;
                  this.value  = ed.value;
                  this.data   = page.params;
                  this.fetch_query = page.url.search
                  //console.log(page.url);
                  //console.log('dataxxxx', this.data, JSON.stringify(this.data))
                  this.message = 'parse_routingFromSveltePage'
                  this.message = ed;
            } catch (error) {
                  //this.message = error;
                  console.log(error);
            }
      }


      /**
       * Destruct the given URL:string to be reused 
       * @param {*} urlString 
       */
      parse_url_string(urlString) {
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


      set_data(data) {
            if (!data) return;
            this.data = data
            this.dataIsFormData = data instanceof FormData ? true : false;
            this.message = "data is set"
      }


      set_best_method(data = undefined, method = undefined) {
            try {
                  let msgAdd = ''
                  if (method != undefined) {
                        // override method {
                        msgAdd = 'override'
                        this.method = method
                  }
                  else if (this.action) {
                        msgAdd = 'auto'
                        this.method = 'POST'
                  }
                  else if (this.data) {
                        msgAdd = 'auto'
                        this.method = 'POST'                        
                  }
                  this.message = "set_best_method "+msgAdd+": "+this.method;
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            }
      }
      

      /**
       * Prepare everything to exec fetch(ZeeltePHP)
       */
      prepare() {
            try {
                  this.message = 'prepare()' + this.message
                  // first set defaults
                  // do not set fetch_url here - to allow override and reuse prepare()
                  //// is set at class definition -> base_url = PUBLIC_ZEELTEPHP_BASE; or override
                  // do not set best method here - to allow override and reuse prepare()
                  //// is done at constructor only once -> this.set_best_method(method);
                  
                  // aka, decode to be ready-to-go for fetch  a la IZP_ApiRouter(Svelte<>Zeelte_PHP)
                  // now supported for GET, POST,
                  //    roadmap PUT, PATCH, ...
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
                  switch (this.method) {
                        case 'GET': 
                                    this.message = 'prepare( ZP_ApiRouter(Svelte<->ZeeltePHP :GET))' 
                                    // we now only need to define the full fetch_url and empty fetch_options
                                    // create URL-query-string interface ZPI_ApiRouter
                                    // set the final fetch_url
                                    ////this.fetch_url = this.base_url + this.fetch_query
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
                                    this.message = '//prepare( ZP_ApiRouter(Svelte<->ZeeltePHP :GET))' 
                              break;
                        case 'POST': 
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
                              break;


                        default:
                                    this.message = 'unknown method '+ this.method;
                                    this.method  = 'unknown';
                              break;
                  }
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            }
      }

}

