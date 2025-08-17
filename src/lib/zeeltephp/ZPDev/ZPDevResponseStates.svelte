<script>
// ZPDevResponseStates.svelte
     import { writable } from "svelte/store";
     import { 
          promise_fetch,
          data, form, error,
          zpAR_pageJS, zpAR_php 
     } from './zpdev.stores.js';

     const debug = false;

     let dashboardStates = $derived({
          http_status: $promise_fetch?.status ?? 0,
          sphpFound  : !$zpAR_php ? 0 : $zpAR_php?.routeFiles == 0 ? 2 : 1,
          phpError   : $error ? 3 : 1,
          phpDataReceived: $data || $form || $error ? 1 : 2
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

     function updateDashboardStates() {
          console.log('updateDashboardStates()', $data, $form, $error);
            dashboardStates.update((states => ({
               ...states,
               phpError: $error ? 3:1,
          })))
     }

     function getResponseCodeDetails() {
          // get code and message
     }

</script>

<ul class="status-list">
     <li>
          <span class="icon">{dashboardStates.http_status}</span>
          <span class="desc">Code</span>
     </li>
     <li>
          <span class="icon">{@html getIconState(dashboardStates.sphpFound)}</span>
          <span class="desc">+.server</span>
     </li>
     <li>
          <span class="icon">{@html getIconState(dashboardStates.phpDataReceived)}</span>
          <span class="desc">data received</span>
     </li>
     <li>
          <span class="icon">{@html getIconState(dashboardStates.phpError)}</span>
          <span class="desc">error</span>
     </li>
</ul>