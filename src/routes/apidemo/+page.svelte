<script>

     import { zp_fetch_api } from "$lib/zeelte/zp.fetch.api.js";

     let data_load;

</script>


<div class="tableFrame">
     <div>
          <button 
               on:click={ async (e) => {
                    data_load = await zp_fetch_api(fetch, 'api/apidemoNONE/', undefined, 'GET')
               }}
          >
               None
          </button>
          <button 
               on:click={ async (e) => {
                    data_load = await zp_fetch_api(fetch, 'api/apidemo/', undefined, 'GET')
               }}
          >
               GET
          </button>
          <button 
               on:click={ async (e) => {
                    data_load = await zp_fetch_api(fetch, 'api/apidemo/', undefined, 'POST')
               }}
          >
               POST
          </button>
          <button 
               on:click={ async (e) => {
                    data_load = await zp_fetch_api(fetch, 'api/apidemo/', undefined, 'PUT')
               }}
          >
               PUT
          </button>
     </div>
     <div>
          Result:
          <pre>{JSON.stringify(data_load, null, 2)}</pre>

     </div>
</div>



<style>

     .tableFrame {
          display: grid;
          grid-template-columns: 150px auto;
     }

</style>