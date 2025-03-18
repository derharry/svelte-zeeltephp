<script>
    import { page } from "$app/state";


      import VarDump from "$lib/VarDump.svelte";
      import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
      import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";
      import { onMount } from "svelte";


      let promise_load
      let data_load



      onMount(() => {
            promise_load = zp_fetch_api(fetch)
                  .then((data) => data_load = data)
                  .catch((error) => data_load = error)
      })



      function handle_dbwp (e) {
            try {
                  data_load    = null
                  /*
                  const ed = new ZP_EventDetails(e);
                  data_load = ed;
                  ed.dump()
                  const zp = new ZP_ApiRouter(e);
                  zp.dump();
                  */
                  promise_load = zp_fetch_api(fetch, e)
                        .then((data) => data_load = data)
                        .catch((error) => data_load = error)
            } catch (error) {
                  data_load = error
                  console.log(error);
            }
      }

</script>

<h1>DB Connections</h1>

<button
      on:click={handle_dbwp}
      formaction="?/testWPDB"
      name="hi"
      value="69"
>
      {#await promise_load}
            requesting db..
      {:then _}
            Test WP-DB
      {:catch error}
            upsie WP-DB <br>{error.message} {error.error}
      {/await}
</button>

<VarDump title="response" vardump={data_load}/>