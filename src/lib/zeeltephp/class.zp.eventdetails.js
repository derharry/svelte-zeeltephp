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
                  if (!event || event == null || event == undefined) { this.message = 'no-event'; return; }  //  event is_nullish
                  if (event instanceof ZP_EventDetails) return event;
                  console.log('ZP_EventDetails()', event)

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

      parse_SubmitEvent() {
            // event is typeof HTMLElementForm | FormSubmit?
            this.message = 'SubmitEvent'
            this.parse_form(this.event.target)
            this.data = new FormData(this.event.target)
            this.dataIsFormData = true;
            this.parse_button(this.event.submitter)
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
            console.log(' @@ target     ', typeof this.event.target)
            console.log(' @@ src Element', typeof this.event.srcElement)
            this.parse_form(this.event?.target);
            this.parse_button(this.event?.srcElement);
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
            this.formData   = new FormData(form)
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