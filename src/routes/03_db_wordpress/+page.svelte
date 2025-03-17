<script>

      import VarDump from "$lib/VarDump.svelte";
      import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";


      let promise_load
      let data_load


      function handle_dbwp (e) {
            try {
                  data_load    = null
                  promise_load = zp_fetch_api(fetch, e)
                        .then((data) => data_load = data)
                        .catch((error) => console.log(error))
            } catch (error) {
                  console.log(error);
            }
      }

</script>

<h1>DB Connections</h1>

<button
      on:click={handle_dbwp}
      formaction="?/testWBDB"
>
      {#await promise_load}
            requesting db..
      {:then _}
            Test WP-DB
      {:catch}
            upsie WP-DB
      {/await}
</button>

<VarDump title="response" vardump={data_load}/>