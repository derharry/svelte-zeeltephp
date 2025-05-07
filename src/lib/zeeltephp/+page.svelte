<script>
     import { onMount } from "svelte";
     import { zp_fetch_api } from "zeeltephp";
     import { VarDump } from "zeeltephp";
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import { ZP_ApiRouter } from "zeeltephp";

     export let data;

     let promise_load;

     onMount(() => {
          console.log(" >> +page.svelte/onMount");
          console.log("  @ export let data (from +page.js)");
          console.log(data);

          console.log("  @ data from onMount/zp_fetch_api (from +page.svelte)");
          let zpAR = new ZP_ApiRouter();

          promise_load = zp_fetch_api(fetch, zpAR);
     });

     function handle_form_submit(event) {
          promise_load = zp_fetch_api(fetch, event);
     }

     function handle_custom_submit(event) {
          //roadmap
     }

     function handle_button_click(event) {
          //roadmap
     }
</script>

<h1>Welkom to ZeeltePHP (<small>a SvelteKit-library project</small>)</h1>

<table>
     <tbody>
          <tr>
               <td>PUBLIC_ZEELTEPHP_BASE</td>
               <td
                    ><a href={PUBLIC_ZEELTEPHP_BASE} target="_blank"
                         >{PUBLIC_ZEELTEPHP_BASE}</a
                    ></td
               >
          </tr>
          <tr>
               <td>Show /src/routes/+page.server.php</td>
               <td>
                    <a href="{PUBLIC_ZEELTEPHP_BASE}?//" target="_blank">
                         {PUBLIC_ZEELTEPHP_BASE}?//
                    </a>
               </td>
          </tr>
          <tr>
               <td>Promise</td>
               <td>
                    {#await promise_load}
                         Loading Promise...
                    {:then data}
                         <VarDump
                              title="promise_load/zp_fetch_api()/promise-data"
                              vardump={data}
                         />
                    {:catch error}
                         <VarDump
                              title="promise_load/zp_fetch_api()/promise-ERROR"
                              vardump={data}
                         />
                    {/await}
               </td>
          </tr>
     </tbody>
</table>

<p>
     Visit <a href="https://github.com/derharry/svelte-zeeltephp"
          >https://github.com/derharry/svelte-zeeltephp</a
     > to read the documentation
</p>
