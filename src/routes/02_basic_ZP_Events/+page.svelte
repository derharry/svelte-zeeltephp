<script>

      import VarDump from '$lib/VarDump.svelte';
      import { ZP_ApiRouter } from '$lib/zeeltephp/class.zp.apirouter';
      import { ZP_EventDetails } from '$lib/zeeltephp/class.zp.eventdetails';
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";

      let _vardump;

      
      function handle_click_ZP_EventDetails(e) {
            try {
                  _vardump = new ZP_EventDetails(e);
            } catch (error) {
                  _vardump = error;
            }
      }

      function handle_click_ZP_ApiRouter(e) {
            try {
                  _vardump = new ZP_ApiRouter(e);
            } catch (error) {
                  _vardump = error;
            }
      }

         
      function handle_click_ZP_ApiRouter_PHP(e) {
            try {
                  promise_load = zp_fetch_api(fetch, e)
                        .then((data) => _vardump = data)
                        .catch((error) => _vardump = error)
            } 
            catch (error) {
                  _vardump = error;
            }
      }
</script>

<h1>Basic Events</h1>

<button
      on:click={handle_click_ZP_EventDetails}
      name="testBtnZP_EventDetails"
      value="42"
      formaction="?/testBtn"
>
      ZP_EventDetails
</button>

<button
      on:click={handle_click_ZP_ApiRouter}
      name="testBtn_ZP_ApiRouter"
      value="42"
      formaction="?/testBtn"
>
      ZP_ActionDetails 
</button>

<button
      on:click={handle_click_ZP_ApiRouter_PHP}
      name="testBtn_ZP_ApiRouter_PHP"
      value="69"
      formaction="?/getZPAR"
>
      ZP_ActionDetails (PHP)
</button>

<VarDump title="Vardump any" vardump={_vardump} />
