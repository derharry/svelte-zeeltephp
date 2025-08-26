<script>
//ZPDevAppPageServer.svelte
     import { invalidate, invalidateAll } from '$app/navigation';
     import { promise_fetch, init_ZPDev, zpAR_svelte } from "./zpdev.stores"
     import { zp_fetch      } from "zeeltephp";
     import { tinyid        } from "zeelte";

     const debug = false;

     /** Action name for API testing */
     let zp_action = $state("action")

     /** Action value for API testing */
     let zp_value = $state("42")

     /** Example data payload for API calls */
     let zp_data = $state({
          name: tinyid(4),
          any:  tinyid(4)
     })

     /**
      * Handels button actions for calls to load, actions, invalidateAll
      * @param e Event object
      */
     function handle_btnAction(e = null) {
          e.preventDefault()
          init_ZPDev(e)
          debug && console.log('# handle_btnAction()')
          $promise_fetch = zp_fetch(e)
          debug && console.log('/ handle_btnAction()')
     }
     
     /**
      * Sends form data as JSON to the backend.
      * @param event Event object
      */
     function handle_sendFormAsJson(event) {
          event.preventDefault()
          debug && console.log('# handle_sendFormAsJson')
          init_ZPDev(event)
          zpAR_svelte.data = zp_data
          $zpAR_svelte.prepare()
          $promise_fetch = zp_fetch(event)
          debug && console.log('  / handle_sendFormAsJson')
     }

</script>

<div class="">
     <button
          name="(re)load"
          onclick={(e) => {
               init_ZPDev(e);
               invalidate((url) => true)
          }}
     >load()</button>
     <button
          name  = "btnAction"
          value = {zp_value}
          formaction = "?/{zp_action}"
          onclick={handle_btnAction}
     >?/{zp_action}()</button>
     <button
          name="btnInvalidateAll"
          value={zp_value}
          formaction="?/{zp_action}"
          onclick={async (e) => {
               debug && console.log('#/ invalidateAll()');
               init_ZPDev(e);
               $promise_fetch = invalidateAll()
          }}
     >invalidateAll()</button>
</div>

<div class="input-row">
     <span>?/</span>
     <input type="text" name="btnActionName"  bind:value={zp_action}  />
     <span>=</span>
     <input type="text" name="btnActionValue" bind:value={zp_value}  />
</div>

<div class="input-row">
     <button
          type="submit"
          name="btnSubmit"
          formaction="?/submit_Form"
          style="width:50%"
     >send-form</button>
     <button
          type="button"
          name="btnSendJson"
          formaction="?/send_data_json"
          style="width:50%"
          onclick={handle_sendFormAsJson}
     >send-json</button>
</div>

<div class="input-row">
     <input type="text" name="name" bind:value={ zp_data.name } />
     <input type="text" name="any"  bind:value={ zp_data.any }  />
</div>