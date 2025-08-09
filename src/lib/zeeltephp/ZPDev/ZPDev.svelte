<script>
// ZPDev.svelte

     import './zpdev.css'

     import { derived    } from 'svelte/store';
     import { invalidate } from '$app/navigation';

     // ZP related
     import { ZP_ApiRouter }    from "$lib/zeeltephp/class.zp.apirouter.js";
     import { ZP_EventDetails } from "$lib/zeelte/class.zp.eventdetails.js";
     import { zp_fetch, data, form, error } from "$lib/zeeltephp/zp.fetch.js";

     // Component related
     import { 
          showApp, showDumpPanel,
          dataDumpPanel,
          zpED_svelte,
          zpAR_svelte,
          resetLocalStores,
     } from './zpdev.stores.js';
     import ZPDevDDS              from "./ZPDevDDS.svelte";
     import ZPDevNav             from "./ZPDevNav.svelte"
     import ZPDevResponseStates  from "./ZPDevResponseStates.svelte";
     import ZPDevAppPageServer   from "./ZPDevAppPageServer.svelte";
     import ZPDevAppServer       from "./ZPDevAppServer.svelte";
     import ZPDevAppDB           from "./ZPDevAppDB.svelte";

     const debug = true
     
     /** Promise for API fetch (for Svelte's #await) */
     let promise_fetch = $state()

     let setVariablesToInit;

     /**
      * Initializes dashboard and API state before a fetch.
      * @param e Optional event object
      */
     function init_ZPDev(event = undefined) {
          //debug &&
          console.clear();
          debug && console.log('# init_ZPDev()')
          event.preventDefault()
          resetLocalStores()
          // show the manually the pre steps of zp_fetch(event) ..
          zpED_svelte.set(new ZP_EventDetails(event))
          zpAR_svelte.set(new ZP_ApiRouter(zpED_svelte))
          // show correct DumpPanel
          debug && console.log('# setDumpPanel()')
          if ($zpED_svelte.action) {
               showDumpPanel.set('form')
          }
          else 
               showDumpPanel.set('data')
          debug && console.log('/ init_ZPDev()')
     }

</script>

<ZPDevDDS 
     bind:promise_fetch
     bind:setVariablesToInit
/>

<form 
     class="frameParent" 
     onsubmit={(e) => {
          console.log('# form/handle-submitForm()')
          init_ZPDev(e)
          promise_fetch = zp_fetch(e)
          console.log('/ form/handle-submitForm()')
     }}
>

     <!-- HEAD -->
     <ZPDevNav bind:promise_fetch />

     <!-- MAIN -->
     <div class="zpdev-tab-dump">

          <!-- .-tab Apps -->
          <div class="contentPadding zpdev-apps svcolor-editor">
               <ZPDevResponseStates />
               <hr />
               {#if $showApp == "PAGE.SERVER.PHP"}
                    <ZPDevAppPageServer {init_ZPDev} bind:promise_fetch />
               {:else if $showApp == "SERVER.PHP"}
                    <ZPDevAppServer {init_ZPDev} bind:promise_fetch />
               {:else if $showApp == "DB"}
                    <ZPDevAppDB {init_ZPDev} bind:promise_fetch />
               {:else}
                    <div>
                         newApp {$showApp}
                    </div>
               {/if}
               <!--
               {:else if $showApp == ".ENV"}
                    <div>
                         <button
                              type="submit"
                              name="btnEnvGet"
                              formaction="?/ENV_get"
                              style="width:50%"
                         >Get .ENV</button>
                    </div>
               {:else if $showApp == "ROUTES"}
                    <ZPDevAppRoutes />
               -->
          </div>

          <!-- .-dump DumpPanel for data form error -->
          <div class="contentPadding zp-vardump svcolor-preview">

               <span>{$showDumpPanel}: {typeof data}</span>
               <pre style="overflow:auto">{JSON.stringify($dataDumpPanel, null, 2)}</pre>

          </div>
     </div>

</form>