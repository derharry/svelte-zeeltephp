<script>

     import './zpdev.css'

     import { writable } from "svelte/store";

     import { zpAR_pageJS, zpAR_php } from './zpdev.stores.js';
     import { data, form, error } from "$lib/zeeltephp/zp.fetch.js";

     const debug = true;

     /**  Dashboard states for const iconStates[] in getIconState() and the UI-status-indicators */
     const dashboardStates = writable ({
          validPhpResponse: 0,
          phpFileFound:     0,
          phpError:         0,
          phpDataReceived:  0,
     });
          
     /**
      * Returns a status icon based on the state code. Used in UI
      * @param state Status code (0–3)
      * @returns {string} Emoji or symbol
      */
     const getIconState = (state = 0) => {
          debug && console.log('getIconState', state)
          const iconStates = [
               ' · ', //0 init/nothing
               '✅',  //1 ok
               '❌',  //2 error
               '⚠️',  //3 unknown check
          ];
          if (iconStates[state]) return iconStates[state]
          return '?'
     };

     /** 
      * Reactively update dashboard state indicators when data changes or is resetted
      */
     $effect.pre(() => {
          if (!$data) return;
          dashboardStates.set({
               phpFileFound    : !$zpAR_php ? 0 : $zpAR_php?.routeFiles ? 1 : 3,
               phpError        : $error ? 3 : 1,
               phpDataReceived : $data  ? 1 : 2
          })
     })

     const x = $derived.by(() => {
          console.log('derived.byRS()', $data, $form, $error);
          //updateDashboardStates()
          /*

          */

          //dashboardStates.update(phpError = $error ? 3 : 1
          //dashboardStates.update(phpDataReceived = $data ? 1 : 2);  //dashboardStates.validPhpResponse === 1 && dashboardStates.phpFileFound === 1 && !data?.type ? 1 : 0;
          //return $form
     });

     function updateDashboardStates() {
          console.log('updateDashboardStates()', $data, $form, $error);
            dashboardStates.update((states => ({
               ...states,
               phpError: $error ? 3:1,
          })))
     } 

</script>


<ul class="status-list">
     <li>
          <span class="icon">{@html getIconState($dashboardStates.validPhpResponse)}</span>
          <span class="desc">api response <small>php | +page.js</small></span>
     </li>
     <li>
          <span class="icon">{@html getIconState($dashboardStates.phpFileFound)}</span>
          <span class="desc">+.php route found?</span>
     </li>
     <li>
          <span class="icon">{@html getIconState($dashboardStates.phpError)}</span>
          <span class="desc">php error</span>
     </li>
     <li>
          <span class="icon">{@html getIconState($dashboardStates.phpDataReceived)}</span>
          <span class="desc">data received</span>
     </li>
</ul>

<style>


</style>