<script>

     import { zp_fetch, data, form, error } from "$lib/zeeltephp/zp.fetch.js";
     import { zp_page_route } from "$lib/zeeltephp/zp.tools.js";

     let {
          init_ZPDev,
          promise_fetch = $bindable()
     } = $props();

     /** */
     let url_api  = zp_page_route()

     function wrap_fetch(e, method) {
          e.preventDefault()
          init_ZPDev(e)
          promise_fetch = zp_fetch('/zpdev', { method: method })
          /*
          -- the origin from to using data,form,error Stores and new response-layer 25-08-06,
          -- - within zeeltephp/zp_fetch.js // data, form, error
          
               .then((dataResponse) => {
                    if (dataResponse?.data) {
                         data  = dataResponse.data
                         form  = dataResponse?.form || {}
                         error = dataResponse?.error || {}
                         console.log('----------page------------------', data, form, error)
                         return { data, form, error }
                    }
                    console.log('----------api------------------', data, form, error)
                    return data
               })
               .catch((error) => {
                    zpAR_php = error;
                    console.error(error);
               });
          */
     }

</script>

<div>
     <div class="input-row">
          url: <input type="text" name="urlApi"  bind:value={url_api} />
     </div>
     <button on:click={(e) => {  wrap_fetch(e, 'GET'    )  }}>GET</button>
     <button on:click={(e) => {  wrap_fetch(e, 'POST'   )  }}>POST</button>
     <button on:click={(e) => {  wrap_fetch(e, 'PUT'    )  }}>PUT</button>
     <button on:click={(e) => {  wrap_fetch(e, 'PATCH'  )  }}>PATCH</button>
     <button on:click={(e) => {  wrap_fetch(e, 'DELETE' )  }}>DELETE</button>
     <button on:click={(e) => {  wrap_fetch(e, 'HEAD',  )  }}>HEAD</button>
</div>