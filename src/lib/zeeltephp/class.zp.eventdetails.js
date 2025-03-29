//import { base } from "$app/paths";
import { page } from '$app/state';
import { ZP_ApiRouter } from './class.zp.apirouter';


// function get_form_event_action_details(event) {
export function get_event_action_details(event) {
return new ZP_EventDetails(event);
}

/**
 * Destructs details of most Event Types to have all in 1 Zeelte structure :-)
 */
export class ZP_EventDetails {

      message     // state/debug message
      route       // current route of page
      event       // source of event

      name        // name of source e.g. button
      action      // formaction
      value       // value of formaction
      data        // data - formData, null, 

      // event src is Button
      button
      srcElement

      // event src is HTMLForm
      form
      dataIsFormData = null
      encoding
      enctype
      

      //wherefor used?
      target


    /**
     * 
     * @param { app-page | URLSearchParams | SubmitEvent | PointerEvent | ZP_ApiRouter} event 
     * @returns 
     */
      constructor(event) {
            try {
                  //# if instance of self - just return self
                  if (!event || event == null || event == undefined) { return false; }  //  event is_nullish
                  if (event instanceof ZP_EventDetails) return event;
                  //console.log('ZP_EventDetails()', event)

                  //# set defaults
                  this.event   = event  // set src
                  //this.target  = event?.target || undefined 
                  //this.srcElement = event.srcElement || undefined

                  //# set current routing as minimum requirement
                  this.route   = page?.url.pathname || page.route.id +'/';
                  //page?.route.id+'/'; // todo: page.url.pathname or page.route.id to be preferred!

                  //# ready
                  this.message = 'init'
                  //this.parse_keyboard();
                  //this.parse_mouse();

                  // parse event
                  if (event instanceof SubmitEvent) 
                        this.parse_SubmitEvent()
                  else if (event instanceof KeyboardEvent) 
                        this.parse_KeyBoardEvent()
                  else if (event instanceof PointerEvent) 
                        this.parse_PointerEvent()
                  else if (event instanceof URLSearchParams)
                        return this.parse_URLSearchParams()
                  else if (event instanceof URL)
                        return this.parse_URL()
                  else if (event instanceof ZP_ApiRouter)
                        this.message = 'instance of ZP_ApiRouter. nothing to do.'
                  else
                        this.message = 'Unknown event '+event +' '+typeof event
                  //console.log(this.message)
            } catch (error) {
                  this.message = error.message;
                  console.error({ error })
            }
      }

      dump(subdata = null, sub = false) {
            if (!sub) console.log('---DUMP ZP_EventDetails-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
            if (value !== undefined && value !== null) 
                  // if (typeof value == 'object' && Object.entries(value).length > 0)
                  //      this.dump(value, true)
                  //else 
                        console.log(variable, value);
            });
            if (!sub) console.log('---END DUMP ZP_EventDetails-----------------------------');
      }

      parse_SubmitEvent() {
            // event is typeof HTMLElementForm | FormSubmit?
            this.message = 'SubmitEvent'
            this.parse_form(this.event.target)
            // Display the key/value pairs
            // for (var pair of this.data.entries()) { console.log(pair[0]+ ', ' + pair[1]); }
            this.parse_button(this.event.submitter)
      }

      parse_URL() {
            this.parse_URLSearchParams();
            //const url  = this.event
            //this.route = url?.pathname
            //this.parse_URLSearchParams(url.searchParams)
            this.message = 'parse_URL'
      }

      parse_URLSearchParams() {
            const url  = this.event            
            this.route = url.pathname //.replace(/\/$/, '')+'/' // remove trailing slash
            //this.route   = e.target.pathname
            //this.search  = this.event.search
            const params = url.searchParams
            if (params && params.size > 0) {
                  //parse for ?/action, the rest is data
                  for (const [key, value] of params) {
                        if (key.startsWith('?/')) {
                        this.action = key;
                        this.value  = value;
                        params.delete(key); // remove from params
                        break;
                  }}
                  this.data = Object.fromEntries(params)
            }
            this.message = 'URLSearchParams'
      }


      parse_KeyBoardEvent() {
            const keyEvent = this.event;
            this.keyId = keyEvent.keyCode;
            //this.parse_keyboard()
      }

      parse_PointerEvent() {
            this.message = 'PointerEvent'
            //console.log(' @@ event      ', this.event)
            //console.log(' @@ target     ', typeof this.event.target)
            //console.log(' @@ src Element', typeof this.event.srcElement)
            //this.parse_form(this.event?.target);
            this.parse_button(this.event.srcElement);
            this.form    = this.event
      }

      
      parse_keyboard() {
            this.keyId   = this.event?.keyId   || this.event?.submitter?.keyId || undefined
            this.keyName = this.event?.keyName || this.event?.submitter?.keyId || undefined
      }
      parse_mouse(e) {

      }

      parse_form(form) {
            this.form       = form
            this.formAction = form?.formAction
            this.data       = new FormData(form)
            this.dataIsFormData = true
            this.encoding   = form?.encoding || this.encoding // 'application/x-www-form-urlencoded'
            this.enctype    = form?.enctype  || this.enctype  //'application/x-www-form-urlencoded'
            //console.log('FORM', form)
      }

      parse_button(button) {
            this.button = button
            this.title      = button?.title
            this.name       = button?.name
            this.value      = button?.value
            this.action     = button?.formAction && button.hasAttribute('formaction') ? button.getAttribute('formaction') : undefined

            this.encoding   = button?.encoding || this.encoding
            this.enctype    = button?.enctype  || this.enctype
            //console.log('BUTTON', button)
      }
}