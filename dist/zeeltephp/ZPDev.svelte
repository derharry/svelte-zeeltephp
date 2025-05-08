<script>
     //import "../../app.css";
     import { PUBLIC_ZEELTEPHP_BASE } from "$env/static/public";
     import { onMount } from "svelte";
     import { page } from "$app/state";
     import { ZP_ApiRouter } from "../zeelte/class.zp.apirouter.js";
     import { ZP_EventDetails } from "../zeelte/class.zp.eventdetails.js";
     import { zp_fetch_api } from "../zeelte/zp.fetch.api.js";
     import VarDump from "../zeelte/VarDump.svelte";
     import Loader from "./Loader.svelte";
     import { invalidateAll } from "$app/navigation";

     /**
      * use the usual export let data; directly from page.data.
      */
     let data = page.data;

     /**
      * disable load at onMount()
      */
     export let noOnmount = false;

     /**
      * by default show only if setup is ok
      * new idea: show only required debug info and icons if ok or not ok  ZP-attributes
      */
     //export let showAll = false;


     let promise_fetch;
     let zpAR_svelte;
     let zpAR_php;
     let zpED_svelte;


     onMount(() => {
          if (!noOnmount) load();
     });

     function reset_data() {
          promise_fetch = undefined;
          zpAR_svelte = undefined;
          zpAR_php = undefined;
          zpED_svelte = undefined;
          data = undefined;
     }

     function load() {
          console.clear();
          reset_data();
          zpAR_svelte = new ZP_ApiRouter();
          zpAR_svelte.prepare();
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
               .then((datax) => {
                    post_api_response(datax);
               })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
     }

     async  function btnInvalidate(event) {
          console.clear();
          reset_data();
          await invalidateAll()
          /*
          promise_fetch = new Promise((resolve, reject) => {
               setTimeout(() => {
                    resolve("foo");
               }, 500);
          })
          .then(value => {})
          */
     }

     function zped(event) {
          console.clear();
          reset_data();
          zpED_svelte = new ZP_EventDetails(event);
          zpAR_svelte = new ZP_ApiRouter(event);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte);
     }

     function btnAction(event) {
          console.clear();
          reset_data();
          zpED_svelte = new ZP_EventDetails(event);
          zpAR_svelte = new ZP_ApiRouter(event);
          promise_fetch = zp_fetch_api(fetch, zpAR_svelte)
               .then((datax) => {
                    post_api_response(datax);
               })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
     }

     function submitForm(e) {}

     function post_api_response(datax) {
          if (datax?.zpAR) {
               zpAR_php = datax.zpAR;
               delete datax.zpAR;
          }
          if (datax?.zpED) {
               zpED_php = datax.zpED;
               delete datax.zpED;
          }
          if (datax?.db) {
               zpED_php = datax.db;
               delete datax.db;
          }
          data = datax;
     }
</script>

<div class="" style="">

<form>
     <button on:click={load} name="(re)load"> load() </button>
     <button on:click={btnInvalidate} name="invalidate"> invalidate() </button>
     <button on:click={btnAction} formaction="?/foo" name="actionBtn" value="bar">
          ?/action
     </button>
     <button on:click={submitForm} formaction="?/example" name="zpED">
          form_submit()
     </button>
</form>

<!--
<table>
     <tbody>
          <tr>
               <td width="50">+.php file found</td>
               <td width="50">
               </td>
          </tr>
     </tbody>
</table>
-->

<table>
     <tbody>
          <tr>
               <th width="50">Svelte (frontend)</th>
               <th width="50">
                    {#await promise_fetch}
                         <Loader />
                    {:then _} 
                         {#if zpAR_php && zpAR_php?.routeFileExist}
                              ✅
                         {:else}
                              ⚠️
                         {/if}
                    {/await}
                    PHP (backend)
               </th>
          </tr>
          <tr class="top">
               <td><VarDump title="zpAR" vardump={zpAR_svelte} /></td>
               <td><VarDump title="zpAR" vardump={zpAR_php} /></td>
          </tr>
          <tr>
               <td colspan="2">
                    <VarDump title="data" vardump={data} />
               </td>
          </tr>
          <tr class="top">
               <td>
                    <VarDump title="zpED" vardump={zpED_svelte} />
               </td>
               <td>
                    {#await promise_fetch}
                         <Loader value="fetching ZeeltePHP" />
                    {:then datat}
                         {#if !datat}
                              <VarDump
                                   title="PROMISE data (zp_fetch_api)"
                                   vardump='* empty * might be resolved earlier (zp_fetch_api().then(data))'
                              />
                         {:else}
                              <VarDump
                                   title="PROMISE data (zp_fetch_api)"
                                   vardump={datat}
                              />
                         {/if}
                    {:catch error}
                         <VarDump
                              title="promise ERROR (zp_fetch_api)"
                              vardump={error}
                         />
                    {/await}
               </td>
          </tr>
     </tbody>
</table>

<hr />

<table>
     <tbody>
          <tr>
               <td width="50">PUBLIC_ZEELTEPHP_BASE</td>
               <td width="50"
                    ><a href={PUBLIC_ZEELTEPHP_BASE} target="_blank"
                         >{PUBLIC_ZEELTEPHP_BASE}</a
                    ></td
               >
          </tr>
     </tbody>
</table>
</div>