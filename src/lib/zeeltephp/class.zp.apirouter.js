// class.zp.api.router.php
import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
import { dev } from "$app/environment";
import { base } from "$app/paths";
import { json } from "@sveltejs/kit";
import { page } from '$app/state';
import { get_event_action_details } from "./event.helper";


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
      fetch_options = null
      dataIsInstanceOfFormData = false



      /**
       * 
       * @param {*} routeUrl = string:/ form-send/ 
       * @param {*} data     = any
       * @param {*} method   = string:GET, POST, PUT, ... 
       */
      //constructor(routeUrl, data = undefined , method = undefined) {
      constructor(routeUrl = undefined, data = undefined, method = undefined) {
            try {
                  this.environment = dev ? 'dev' : 'build'
                  this.set_routeURL(routeUrl);
                  this.set_data(data);
                  this.set_best_method(data, method)
                  this.prepare()
            } catch (error) {
                  console.error({ error })
            }
      }

      dump() { 
            console.log(this);
      }

      /**
       * Set de default url - default should be URL or PAGE.URL, string if you want to override URL
       * @param {*} url string | URL:+page.js/load | PAGE.URL:+page.svelte 
       */
      set_routeURL(routeUrl) {
            try {
                  // set default current route by page.url.pathname
                  this.route = page.route.id +'/'; 

                  // custom url?route or zp-default-route from url
                  // structure http://url/api?route&?/action=1&x=x&y=y
                  // if starts with ?
                  if (routeUrl == undefined) {
                        // default nothing
                  }
                  else if (typeof routeUrl === 'string') {
                        console.log('STRING', routeUrl)
                        this.parse_url_string(routeUrl)
                  }
                  else if (routeUrl?.pathname) {
                        // parse :URL 
                        //this.zp_baseUrl= PUBLIC_ZEELTEPHP_BASE;
                        this.route = routeUrl.pathname.replace(base, '')
                        console.log('PATHNAME', routeUrl)
                  }
                  else if (routeUrl instanceof SubmitEvent) {
                        console.log('SubmitEvent', routeUrl)
                        const ed = get_event_action_details(routeUrl);
                        this.action = ed.action;
                        this.value  = ed.value;
                        this.set_data(ed.formData);
                  } else {
                        console.log('WHAT', {routeUrl}, typeof routeUrl)
                  }
            } catch (error) {
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

                  /*
                        previous code before parse() and parseResponse()

                        // check first action?
                        if (action) {
                              if (action.startsWith('?/') || action.startsWith('/'))
                                    this.action = action.startsWith('?') ? action : '?'+action
                              else if (action.includes('='))
                                    //second = action +'&'+ second
                                    this.params.push(action)
                              else 
                                    console.log(' ??????? ', {action})
                        }

                        // check route
                        if (route) {
                              if (route.startsWith('?/') || route.startsWith('/')) {
                                    if (this.action == null) {
                                          this.action = route.startsWith('?') ? route : '?'+route
                                    } else
                                          console.log(' ??????? ', {route})
                              } else if (route.includes('=')) {
                                    //second = route +'&'+ second
                                    this.params.push(route)
                              } else {
                                    this.route = route || ''
                              }
                        }

                        if (second) {
                              if (second.startsWith('?/') || second.startsWith('/')) {
                                    if (this.action == null) {
                                          this.action = second.startsWith('?') ? second : '?'+second
                                    } else
                                          console.log(' ??????? ', {second})
                              } else if (second.includes('=')) {
                                    //second = route +'&'+ second
                                    this.params.push(second)
                              } else {
                                    console.log(' ??????? ', {second})
                              }
                        }

                        if (params.length > 0) {
                              console.log(' AAAAAAAA', {params})
                              this.params = this.params.concat(params)
                        }
                        if (third.length > 0) {
                              console.log(' AAAAAAAA', {third})
                              this.params = this.params.concat(third)
                        }
                  */
            } catch (error) {
                console.error({ error });
            }
      }


      set_data(data) {
            if (data) {
                  this.data = data
                  this.dataIsInstanceOfFormData = data instanceof FormData ? true : false;
            }
      }


      set_best_method(data = undefined, method = undefined) {
            try {
                  if (method != undefined)
                        // override method
                        this.method = method
                  else if (this.action) {
                        this.method = 'POST'
                  }
            } catch (error) {
                  console.error({ error })
            }
      }
      

      prepare(method) {
            try {
                  this.set_best_method(method);
                  switch (this.method) {
                        case 'GET': 
                                    //this.fetch_url += this.action == null ? '' : '&?/'+ this.action + this.value == null ? '' : '=' + this.value;
                                    //this.fetch_url += this.params.length == 0 ? '' : '?'+ this.params.join('&')
                                    //this.fetch_url += this.data   == null ? '' : '?' + JSON.stringify(this.data);
                                    const params = [];
                                    // 1st is zp_route
                                    params.push(this.route)
                                    // 2nd is zp_action
                                    if (this.action) {
                                          this.action = '?/' + this.action.replace('?/', '')
                                          params.push(this.action +(this.value ? '='+this.value : '') )
                                    }
                                    // 3rd is any params or data
                                    if (this.params && this.params?.length > 0)
                                          params.push( this.params.join('&'))
                                    if (this.data)
                                          params.push( JSON.stringify(this.data) )
                                    // set fetch_url 
                                    this.fetch_url  = this.zp_baseUrl + '?' + params.join('&');
                                    // no fetchOptions
                                    this.fetch_options = {}
                              break;
                        case 'POST': 
                                    this.fetch_url     = this.zp_baseUrl;
                                    this.fetch_options = {
                                          method:  this.method,
                                          headers: {},
                                          body: null // data
                                    }
                                    // prepare data
                                    if (this.dataIsInstanceOfFormData) {
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
                              this.method = 'unknown';
                  }
            } catch (error) {
                  console.error({ error })
            }
      }

}


