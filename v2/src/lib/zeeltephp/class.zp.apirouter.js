// class.zp.api.router.php
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { dev }  from "$app/environment";
import { base } from "$app/paths";
import { json } from "@sveltejs/kit";
import { page } from '$app/state';
import { get_event_action_details, ZP_EventDetails } from "./class.zp.eventdetails";


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
      zp_baseUrl  = PUBLIC_ZEELTEPHP_BASE;

      // CSR specfic -> zp_fetch_api() 
      fetch_url      = null // final url   to use at zp_fetch_api(fetch_url, fetchOptions)
      fetch_query    = null // final query that holds the part ?.. as query_string
      fetch_options  = null // final options to use at zp_fetch_api(fetch_url, fetchOptions)
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

                  this.set_currentRoutingBySveltePage();
                  this.prepare();

                  /*
                  this.set_routeURL(routeUrl);
                  this.set_data(data);
                  this.set_best_method(data, method);
                  this.prepare();
                  */
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

      // in Svelte we read Sveltes $app/page
      // in PHP we destruct this for ?/route/&?/action=value&query_params
      set_currentRoutingBySveltePage() {
            try {
                  // set default current route 
                  // use ZP_EventDetails to get the details :-)
                  const ed = new ZP_EventDetails(page.url.searchParams);
                  this.route  = ed.route;
                  this.action = ed.action;
                  this.value  = ed.value;
                  this.data   = ed.data;
                  this.fetch_query = page.url.search
                  this.message = 'currentRoutingBySveltePage';
                  //console.log(page.url);
                  //console.log('dataxxxx', this.data, JSON.stringify(this.data))
            } catch (error) {
                  //this.message = error;
                  console.log(error);
            }
      }


      /**
       * Set de default url - default should be URL or PAGE.URL, string if you want to override URL
       * @param {*} url stringURL | URL: +page.js/load({url}),*.svelte/page.url | SubmitEvent 
       */
      set_routeURL(routeUrl) {
            let debug = false;
            try {
                  if (!routeUrl) return;
                  if (debug) console.log('set_routeURL()', routeUrl)


                  // custom url?route or zp-default-route from url
                  // structure http://url/api?route&?/action=1&x=x&y=y
                  // if starts with ?
                  //if (routeUrl == undefined) {
                        // default nothing
                  //}
                  //else 
                  if (typeof routeUrl == 'string') {
                        if (debug) console.log('STRING', routeUrl)
                        this.parse_url_string(routeUrl)
                  }
                  else if (routeUrl instanceof URL && routeUrl?.pathname) {
                        // parse :URL 
                        //this.zp_baseUrl= PUBLIC_ZEELTEPHP_BASE;
                        if (debug) console.log('path.url.replace', routeUrl)
                              this.route = routeUrl.pathname.replace(base, '')+'/';
                  }
                  else {

                        // before writing 
                        //    else if (routeUrl instanceof SubmitEvent || routeUrl instanceof PointerEvent || ... ) { .. }
                        // lets do this else block a action details check 1 time 
                        const ed = get_event_action_details(routeUrl);
                        if (ed) {
                              this.action = ed.action;
                              this.value  = ed.value;
                              this.set_data(ed.formData);
                              this.message = ed;
                        }
                        else {
                              this.message = Array.join(' ', ['set_routeURL is UNKNOWN', {routeUrl}, typeof routeUrl]);
                        }
                  }
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            }
      }


      parse_url_string(urlString) {
            let debug = true;
            try {
                  if (debug) console.log(' () ', urlString);

                  let [first,   second, ...third] = urlString.split('&')
                  let [baseUrl, route,  action, ...params] = first.split('?')
                  
                  if (debug) console.log('1) ', {first}, {second}, {third})
                  if (debug) console.log('2) ', {baseUrl}, {route}, {action}, {params})

                  // set url 
                  this.url = baseUrl || this.url;

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
      }


      set_best_method(data = undefined, method = undefined) {
            try {
                  if (method != undefined)
                        // override method
                        this.method = method
                  else if (this.action && this.data) {
                        this.method = 'POST'
                  }
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            }
      }
      

      prepare(method) {
            try {
                  //this.set_best_method(method);
                  switch (this.method) {
                        case 'GET': 
                                    // we now only need to define the full page url...
                                    //this.fetch_url += this.action == null ? '' : '&?/'+ this.action + this.value == null ? '' : '=' + this.value;
                                    //this.fetch_url += this.params.length == 0 ? '' : '?'+ this.params.join('&')
                                    //this.fetch_url += this.data   == null ? '' : '?' + JSON.stringify(this.data);

                                    // first we need to create our URL-query-string interface ZPI_ApiRouter
                                    const parts_ZPI_ApiRouter = [];
                                    const parts_ZPI_push = (key, value = undefined) => {
                                          //console.log('xxxxx'+key+'='+value);
                                          if (key && typeof key == 'array') {
                                                parts_ZPI_ApiRouter.concat(this.params)
                                          }
                                          if (key && typeof key == 'object') {
                                                for (const [key_, value_] of Object.entries(key)) {
                                                      parts_ZPI_ApiRouter.push(key_+'='+value_)
                                                }
                                          }
                                          else if (key && typeof key == 'string') {
                                                parts_ZPI_ApiRouter.push(key + (value ? '='+value : ''))
                                          }
                                    }

                                    // 1st is zp_route
                                    parts_ZPI_push(this.route)
                                    // 2nd is zp_action
                                    parts_ZPI_push(this.action, this.value)
                                    parts_ZPI_push(this.params)
                                    parts_ZPI_push(this.data)
                                    this.fetch_url  = this.zp_baseUrl + '?' + parts_ZPI_ApiRouter.join('&');
                                    // this.action = '?/' + this.action.replace('?/', '')
                                    this.fetch_options = undefined
                              break;
                        case 'POST': 
                                    this.fetch_url     = this.zp_baseUrl;
                                    this.fetch_options = {
                                          method:  this.method,
                                          headers: {},
                                          body: null // data
                                    }
                                    // prepare data
                                    if (this.dataIsFormData) {
                                          // add form content-type
                                          // don't set - it works out of the box :-O this.fetch_options.headers['Content-Type'] = 'multipart/form-data';
                                          this.fetch_options.body = new FormData();
                                          this.fetch_options.body.append('zp_route', this.route)
                                          this.fetch_options.body.append('zp_action', this.action)
                                          this.fetch_options.body.append('zp_value', this.value)
                                          //this.fetch_options.body.append(this.data)
                                          for (let [key, val] of this.data.entries()) {
                                                this.fetch_options.body.append(key, val)
                                          };
                                    } else {
                                          // add fallback to all json content-type
                                          this.fetch_options.headers['Content-Type'] = 'application/json';
                                          this.fetch_options.body = JSON.stringify({
                                                zp_route : this.route,
                                                zp_action: this.action,
                                                zp_value : this.value,
                                                zp_data  : this.data
                                          });
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


