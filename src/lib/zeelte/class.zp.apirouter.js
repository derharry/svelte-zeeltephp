// class.zp.api.router.php
import { dev }  from "$app/environment";
import { base } from "$app/paths";
import { page } from '$app/state';
import { ZP_EventDetails } from "./class.zp.eventdetails.js";
import { zp_page_route } from "../zeeltephp/zp.tools.js";
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";

/**
 * Api for communication between Svelte <-> ZeeltePHP.
 * Handles route/action/value/data extraction, fetch URL and options preparation,
 * and supports multiple construction patterns for CSR/SSR and event-driven requests.
 *
 * Usage:
 *   new ZP_ApiRouter();                    // detects routing from Svelte page
 *   new ZP_ApiRouter(event);               // detects routing, formAction, data to send, etc from event details via ZP_EventDetails
 *   new ZP_ApiRouter(ZP_EventDetails);     // uses the settings from ZP_EventDetails
 */
export class ZP_ApiRouter 
{
      // --- Routing and action properties ---

      /** @type {string|null} Route path (e.g. /foo/bar/) */
      route = null;
      /** @type {string|null} Action name (e.g. ?/ACTION) */
      action = null;
      /** @type {any} Action value */
      value = null;
      /** @type {any} Query params */
      params = null;
      /** @type {any} Data payload (formData, JSON, etc.) */
      data = null;

      // --- Svelte/CSR-specific properties ---

      /** @type {string} Environment flag ('dev' or 'prod') */
      environment = 'dev | build';
      /** @type {string} HTTP method ('GET', 'POST', etc.) */
      method = 'GET';

      // --- Fetch preparation ---

      /** @type {string} Base API URL */
      base_url = PUBLIC_ZEELTEPHP_BASE; // import.meta.env.PUBLIC_ZEELTEPHP_BASE
      /** @type {string|null} Final fetch URL */
      fetch_url = null;
      /** @type {string|null} Query string for fetch */
      fetch_query = null;
      /** @type {object|null} Fetch options (method, headers, body, etc.) */
      fetch_options = null;
      /** @type {string} Default content type */
      fetch_enctype = 'application/json';
      /** @type {boolean} True if data is FormData */
      dataIsFormData = false;
      /** @type {string} Debugging last_message */
      last_message = "";

      // --- Internal/debug ---
      debug      = false;
      debug_msgs = [];

      /**
       * Add a debug message (if debug mode is enabled).
       * @param {string} msg
       * @param {any} value
       */
      log(msg, value = '') {
            this.last_message = msg;
            if (this.debug) {
                  this.debug_msgs.push({msg, value});
                  console.log('  ', msg, value);
            }
      }

      /**
       * Constructor: Overloaded for multiple use-cases. See zp_fetch_api() for details.
       * @param {*} router  Can be ZP_ApiRouter, string, event, or undefined
       * @param {*} data    Optional, force data payload (overrides auto-detected)
       * @param {string} method Optional, force HTTP method (GET, POST, etc.)
       * @param {boolean} debug Enable debug mode
       */
      constructor(router = undefined, data = undefined, method = undefined, debug = false) {
            try {
                  if (router instanceof ZP_ApiRouter) return router;
                  this.debug = debug;
                  this.log('-- ZP ApiRouter ');

                  // Set environment
                  this.environment = dev ? 'dev' : 'prod';
                  this.log('env', this.environment);

                  // Set current route from from Svelte page (default)
                  this.route = zp_page_route();

                  // Handle overloading: string, event, any..
                  if (typeof router === 'string')
                        this.parse_routerFromString(router);
                  else {
                        let checked = false; // flag to know if we have a deparsed case

                        // Try parsing event details
                        // ZP_EventDetails knows most use-cases e.g. SubmitEvent, PointerEvent, etc
                        if (!checked)
                              checked = this.parse_routerFromZPeventDetails(router);

                        if (!checked) {
                              this.log('  ! unsupported router');
                        }
                  }
                  
                  // Ensure route starts/ends with /
                  this.route = '/' + (this.route || '') + '/';
                  this.route = this.route.replace(/\/\//g, '/');

                  // finalize
                  this.log('finalize');
                  this.set_data(data);
                  this.set_best_method(method);
                  // -- this.prepare(); // is called in set_best_method();
                  //this.log(this);
            } catch (error) {
                  this.log(error);
            }
      }

      /**
       * Dumps the current state to the console (for debugging).
       */
      dump() {
            console.log('---DUMP ZP_ApiRouter-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
                  if (value !== undefined && value !== null)
                  console.log(variable, value);
            });
            console.log('---END DUMP ZP_ApiRouter-----------------------------');
      }

      /**
       * Parse/decode the string for the routing details.
       * @param {string} router 
       */
      parse_routerFromString(router) {
            this.log('parse_routerFromString()', router);
            // new feature preparation (+.api.php)
            // -- if (router.startsWith('+')) {
            // --      this.log('ZP_ApiRouter-api/**');
            // --      this.route = router;
            // -- } else {
                  // decode the string - assumed to be a GET-URL string to deparse
                  this.parse_routerFromString(router);
            // --}
      }

      /**
       * Parse routing/action/value/data from a ZP_EventDetails object or event.
       * @param {*} event
       * @returns {ZP_EventDetails|false}
       */
      parse_routerFromZPeventDetails(event) {
            this.log('parse_routerFromZPeventDetails');
            const zpED = new ZP_EventDetails(event, this.debug);
            if (zpED) {
                  this.route  = zpED.route  || this.route;
                  this.action = zpED.action || this.action;
                  this.value  = zpED.value  || this.value;
                  this.set_data(zpED.data);
                  this.fetch_query = page.url.search || this.fetch_query;
                  // -- ??? this.fetch_enctype = zpED.encoding || this.fetch_enctype
                  return zpED;
            }
            return false;
      }

      /**
       * Sets the data payload and detects if it's FormData.
       * @param {*} data
       */
      set_data(data) {
            if (!data) return;
            this.log('set_data');
            this.data = data;
            this.dataIsFormData = data instanceof FormData;
      }

      /**
       * Determines the best HTTP method based on the action/data, or uses the forced method.
       * @param {string|undefined} forceMethod
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
       * Prepares the fetch_url and fetch_options for the current state.
       */
      prepare() {
            this.log('prepare()')
            // supported
            //    GET  = URL :string      = PUBLIC_ZEELTEPHP_BASE?/route/&?/action=value&any
            //                ?/route/        : required so ZeeltePHP can find route/+page.server.php 
            //                ?/action=value  : optional action from formAction or override, value : optional
            //                &any_params_data: this.params or just this.fetch_query
            //                                  this.data data is not supported or we push key_values into the get
            //    POST = ZP_ApiRouter :JSON 
            //                zp_route  = ?/route/
            //                zp_action = ?/action
            //                zp_value  = =value
            //                zp_data   = &any
            //    PUT, PATCH, ... 
            
            // -info- keep set_best_method() at __construct; do not set fetch_url here; -> otherwise manuall (override) settings are overwriten.
            if      (this.method == 'GET')  this.prepare_GET()
            else if (this.method == 'POST') this.prepare_POST();
            else     this.log(' - unsupported RequestType');
      }

      /**
       * Prepares a GET request (URL with query string).
       */
      prepare_GET() {
            this.log('prepare_GET')
            const zpURL = []
            const zpURLPush = (data = null) => {
                  if (data !== null && data !== undefined) {
                        if (Array.isArray(data)) {
                              zpURL.push(...data);
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
                  }
            }

            // add zp_route
            zpURLPush(this.route)

            // add zp_action and zp_value
            let actionStr = this.action
            if (actionStr && this.value) actionStr += '=' + this.value
            zpURLPush(actionStr)
            zpURLPush(this.data)
            // --info-- params is already pushed to data! and ignored from now on  -- zpURLPush(this.params);

            // set baseUrl + zp_route/&
            this.fetch_url = this.base_url +'?'+ zpURL.join('&');
            this.fetch_options = {}
      }

      /**
       * Prepares a POST request (JSON or FormData).
       */
      prepare_POST() {
            this.log('prepare_POST');
            let data = null;

            if (this.data instanceof FormData) {
                  // inject route, action, value into data. 
                  // PHP destructs: $_POST[ zp_route, zp_action, zp_value, ...data ];
                  data = this.data;
                  data.append('zp_route', this.route);
                  data.append('zp_action', this.action);
                  data.append('zp_value', this.value);
                  this.log = 'prepared: POST/FormData';
            } else {
                  // create $_POST[ zp_route, zp_action, zp_value, zp_data ]
                  // PHP destructs: $_POST[ zp_route, zp_action, zp_value, zp_data ];
                  data = JSON.stringify({
                        'zp_route': this.route,
                        'zp_action': this.action,
                        'zp_value': this.value,
                        'zp_data': this.data
                  });
                  this.log = 'prepared: POST/JSON';
            }

            // set baseUrl + zp_route/&
            this.fetch_url     = this.base_url;
            this.fetch_options = {
                  method:     this.method,
                  headers:    {
                        // --info-- don't set 'content-type' here, let it browsers default. 
                        // -- this.fetch_options.headers['Content-Type'] = 'multipart/form-data'; www-urlencoded, ...
                  },
                  body: data // data
            }
      }


      /**
       * *deprecated* 
       * Decodes a URL-string to get the route/action/value/params components.
       * Can be used as url checker.
       * @param {string} urlString
       */
      decode_url_string(urlString) {
            let debug = this.debug;
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

                        if (response.route && !this.route)   this.route  = response.route
                        if (response.action && !this.action) this.action = response.action
                        if (response.value && !this.value)   this.value  = response.value
                        if (response.params?.length > 0)     this.params = (this.params || []).concat(response.params);
                  }
                  parse(action) //parseResponse(parse(action))
                  parse(route)  //parseResponse(parse(route))
                  parse(second) //parseResponse(parse(second))
                  if (params.length > 0) this.params = (this.params || []).concat(params);
                  if (third.length  > 0) this.params = (this.params || []).concat(third);
        } catch (error) {
            this.message = error;
            console.error({ error });
        }
    }
}
