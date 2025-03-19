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
      dataIsFormData
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
                  if (!event) { this.message = 'no-event'; return; }  //  event is_nullish
                  if (event instanceof ZP_EventDetails) return event;

                  //# set defaults
                  this.event   = event  // set src
                  this.target  = event?.target || undefined 
                  this.srcElement = event.srcElement || undefined

                  //# set current routing as minimum requirement
                  this.route   = page?.url.pathname || page.route.id +'/';
                  //page?.route.id+'/'; // todo: page.url.pathname or page.route.id to be preferred!

                  //# ready
                  this.message = 'init'
                  this.parse_keyboard();
                  this.parse_mouse();

                  // parse event
                  if (event instanceof SubmitEvent) 
                        this.parse_SubmitEvent()
                  else if (event instanceof KeyboardEvent) 
                        this.parse_KeyBoardEvent()
                  else if (event instanceof PointerEvent) 
                        this.parse_PointerEvent()
                  else if (event instanceof URLSearchParams)
                        return this.parse_URLSearchParams()
                  else if (event instanceof ZP_ApiRouter)
                        this.message = 'instance of ZP_ApiRouter. nothing to do.'
                  else
                        this.message = 'Unknown event '+event
                  //console.log(this.message)
            } catch (error) {
                  this.message = error;
                  console.error({ error })
            }
      }

      dump() {
            console.log('---DUMP ZP_EventDetails-----------------------------');
            Object.entries(this).forEach(([variable, value]) => {
            if (value !== undefined && value !== null) 
                  console.log(variable, value);
            });
            console.log('---END DUMP ZP_EventDetails-----------------------------');
      }

      parse_SubmitEvent(e) {
            this.message = 'SubmitEvent'
            // event is typeof HTMLElementForm | FormSubmit?
            this.parse_form(e.target)
            this.parse_button(e.submitter)
      }

      parse_URLSearchParams() {
            this.message = 'URLSearchParams'
            const url  = this.event
            this.route = url.pathname
            //this.route   = e.target.pathname
            //this.search  = this.event.search
            if (this.event.size > 0) {
                  this.data = [];
                  for (const [key, value] of this.event) {
                        if (key.startsWith('?/')) {
                              this.action = key;
                              this.value  = value;
                        }
                        else {
                              this.data[key] = value;
                        }
                  }
            }
      }

      parse_KeyBoardEvent() {
            const keyEvent = this.event;
            this.keyId = keyEvent.keyCode;
            //this.parse_keyboard()
      }

      parse_PointerEvent() {
            this.message = 'PointerEvent'
            this.button  = this.event.target
            this.parse_button();
      }

      
      parse_keyboard() {
            this.keyId   = this.event?.keyId   || this.event?.submitter?.keyId || undefined
            this.keyName = this.event?.keyName || this.event?.submitter?.keyId || undefined
      }
      parse_mouse(e) {

      }

      parse_form(form) {
            this.form       = form
            this.formAction = form.action
            this.formData   = new FormData(form)
            this.encoding   = form.encoding // 'application/x-www-form-urlencoded'
            this.enctype    = form.enctype  //'application/x-www-form-urlencoded'

      }

      parse_button() {
            let button      = this.button
            this.title      = button.title
            this.name       = button.name
            this.value      = button.value
            this.action     = button.hasAttribute('formaction') ? button.getAttribute('formaction') : undefined

            this.enctype    = button.enctype
            this.encoding   = button.encoding
      }
}