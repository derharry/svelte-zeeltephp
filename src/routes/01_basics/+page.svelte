<script>
    import { invalidateAll } from '$app/navigation';
    import { page } from '$app/state';


      import VarDump from '$lib/VarDump.svelte';
      import { ZP_ApiRouter } from '$lib/zeeltephp/class.zp.apirouter';
      import { ZP_EventDetails } from '$lib/zeeltephp/class.zp.eventdetails';
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";


      export let data;

      let _vardump = data;
      let _varDumpTitle = '+page.js/load'


      function set_vardump(title, data) {
            _varDumpTitle = title
            _vardump = data
      }

      function handle_click_invalidateAll() {
            invalidateAll();
            _vardump     = null;
            _varDumpTitle = '+page.js/load'
            _vardump = data;
      }
      
      function handle_click_ZP_EventDetails(e) {
            try {
                  _varDumpTitle = 'ZP_EventDetails'
                  _vardump     = null;
                  _vardump = new ZP_EventDetails(e);
            } catch (error) {
                  _vardump = error;
            }
      }

      function handle_click_ZP_ApiRouter(e) {
            try {
                  console.clear();
                  _varDumpTitle = 'ZP_ApiRouter (Svelte)'
                  _vardump     = null;
                  _vardump = new ZP_ApiRouter(e);
            } catch (error) {
                  _vardump = error;
            }
      }

      function handle_click_ZP_ApiRouter_PHP(e) {
            try {
                  console.clear();
                  _varDumpTitle = 'ZP_ApiRouter (PHP)'
                  _vardump     = null;
                  const promise_load = zp_fetch_api(fetch, e)
                        .then((data) => _vardump = data)
                        .catch((error) => _vardump = error)
            } 
            catch (error) {
                  _vardump = error;
            }
      }
</script>


<h1>Basics / Environment</h1>


<div class="tableCompact defaultPadding">
      <div>
            <!--<label>Svelte</label>-->
            <button
                  title="load example hello's from all routes"
                  on:click={handle_click_invalidateAll}
            >
                  Reload
            </button>
            <button
                  on:click={handle_click_ZP_EventDetails}
                  name="btnZpEventDetails"
                  value="42"
                  formaction="?/action"
            >
                  ZP_EventDetails
            </button>
            &nbsp;&nbsp;
            <button
                  on:click={handle_click_ZP_ApiRouter}
                  name="btnZpApiRouter"
                  formaction="?/action"
            >
                  ZP_ApiRouter (Svelte)
            </button>
            <button
                  on:click={handle_click_ZP_ApiRouter_PHP}
                  name="btnZpApiRouter"
                  value="69"
                  formaction="?/getZpApiRouterPHP"
            >
                  ZP_ApiRouter (PHP)
            </button>
      </div>

      <div><br></div>
</div>
<VarDump title="{_varDumpTitle} " vardump={_vardump} />

