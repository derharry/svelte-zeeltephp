<script>

     import './zpdev.css'
     import ZPDevNav               from "./ZPDevNav.svelte"
     import Temp                   from "./Temp.svelte"

     import { zpAR_svelte, zpAR_pageJS, zpAR_php, zpED_svelte, showDumpPanel } from './zpdev.stores.js';
     import { zp_fetch, data, form, error } from "$lib/zeelte/zp.fetch.api.js";
     import { derived } from 'svelte/store';

     const debug = true
     
     /** Promise for API fetch (for Svelte's #await) */
     let promise_fetch = $state()

     /**
      * Resets all API state variables to initial values.
      */
     const setVariablesToInit = () => {
          debug && console.log('  #/ setVariablesToInit()')
          promise_fetch = null
          zpAR_svelte.update(null)
          zpAR_pageJS.update(null)
          zpAR_php   .update(null)
          zpED_svelte.update(null)
          //data = null;
     };     

     /**
      * Initializes dashboard and API state before a fetch.
      * @param e Optional event object
      */
     function init_ZPDev(event = undefined) {
          //debug && console.clear();
          debug && console.log('# init_ZPDev()')
          setVariablesToInit()
          // show the manually the pre steps of zp_fetch(event) ..
          zpED_svelte.update(new ZP_EventDetails(event))
          zpAR_svelte.update(new ZP_ApiRouter(zpED_svelte))
          debug && console.log('/ init_ZPDev()')
     }

     const dataDumpPanel = $derived.by(() => {
          //console.log('derived.by()', $showDumpPanel)
          switch ($showDumpPanel) {
               case 'data':  return $data
               case 'form':  return $form
               case 'error': return $error
          }
          return 'noo'
     });
     
</script>

<form 
     class="frameParent" 
     on:submit={(e) => {
          e.preventDefault()
          console.log('# handle-submitForm()')
          console.log('/ handle-submitForm()')
     }}
>
     <ZPDevNav bind:promise_fetch />

     <!-- MAIN -->
     <div class="zpdev-tab-dump">
          <!-- tab -->
          <div class="contentPadding zpdev-apps svcolor-editor">
          </div>
          <!-- dump -->
          <div class="contentPadding zp-vardump svcolor-preview">

               <span>{$showDumpPanel}: {typeof data}</span>
               <pre style="overflow:auto">{JSON.stringify(dataDumpPanel, null, 2)}</pre>

          </div>
     </div>

     <!-- FOOTER -->
     <div class="zpdev-dump-dump frameFooter">
          <div class="contentPadding zp-vardump svcolor-preview">
               <span>zpAR svelte: {typeof zpAR_svelte}</span>
               <pre style="overflow:auto">{JSON.stringify(zpAR_svelte, null, 2)}</pre>
          </div>
          <div class="contentPadding zp-vardump svcolor-preview">
               <span>zpAR svelte: {typeof zpAR_php}</span>
               <pre style="overflow:auto">{JSON.stringify(zpAR_php, null, 2)}</pre>
               <!--
               <VarDump
                    title="zpAR php"
                    vardump={zpAR_php}
                    dumpJson={showDumpsInJSON}
                    noBorder={true}
               />
               -->
          </div>
     </div>
</form>