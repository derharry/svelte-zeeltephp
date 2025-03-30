<script>
    import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";
    import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";

    import VarDump from "$lib/zeeltephp/VarDump.svelte";
    import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
    import { fromStore } from "svelte/store";

    let form = {
        error    : "",
        message  : "", 
        debug    : null,
        isSend   : false,
        jsonData : false,
        formData : false,
    }

    let data = {
        name    : "",
        phone   : ""
    }
    data.name    = crypto.randomUUID()
    data.phone   = crypto.randomUUID()

    let xform;

    let jsonData;
    let formData;
    
    let promise_sendJson;
    let promise_sendForm;

    function merge_keyValuePairs(target, src) {
            if (typeof target === 'object' && typeof src === 'object') 
                for (const [key,value] of Object.entries(target))
                    if (src[key]) 
                        target[key] = src[key];
            return target;
    }

    function handle_send_json(event) {
        try {
            console.clear();
            console.log('handle_send_json',event);
            form.jsonData = event;

            
            const ed = new ZP_EventDetails(event);
            form.jsonData = ed;
            //const zpar  = new ZP_ApiRouter(event, data);
            //form.jsonData = zpar
            ed.dump();

            return;
            /*
            const zpar  = new ZP_ApiRouter(event, data)
            //zpar.dump();
            promise_sendJson = zp_fetch_api(fetch, event, data)
                .then((data1) => {
                    console.log(data1);
                    form = merge_keyValuePairs(form, data1)
                    return true;
                    console.log(data1);
                    form.jsonData = data1.data
                    form.message  = data1.message;
                })
                .catch((error) => console.error(error))
            */
        }
        catch (error) {
            console.error(error)
        }
        form = form;
        console.log('//handle_send_json()')
    }

    function handle_form_submit(event) {
        const debug = false
        try {
            console.clear();
            console.log('handle_form_submit',event);

            const ed = new ZP_EventDetails(event);
            form.jsonData = ed;
            ed.dump();

            /*
            const za = new ZP_ApiRouter(event);
            form.jsonData = za;
            za.dump();

            
            // form 
            promise_sendForm = zp_fetch_api(fetch, event)
                .then((data) => {
                    console.log('DAAAATTAAA', {data})
                    form.jsonData = data
                    form = form;
                })
                .catch((error) => console.error(error))
            // endform
            /*
            const ed = new ZP_EventDetails(event);
            console.log(ed)
            form.jsonData = ed;
            ed.dump();

            return;
            event.preventDefault();

            const za = new ZP_ApiRouter();
            form.formData = za

            return;
            /*
            const ed = new ZP_EventDetails(event);
            form.jsonData = ed;

            const za = new ZP_ApiRouter(event);
            form.formData = za;

            form = form;
            //data = ed;
            //form.formData = ed.message;
            return
            console.log(ed)

            const zpar = new ZP_ApiRouter(event)
            zpar.prepare()
            //console.log(zpar.fetch_options);
            form.formData = zpar;

            return;
            promise_sendForm = zp_fetch_api(fetch, event, form)
                .then((data1) => {
                    console.log(data1);
                    form.send     = data1.ok
                    form.formData = data1.data
                    form.message  = data1.message;
                })
                .catch((error) => console.error(error))
            if (debug)  console.log('***********************')

            //const zp = zp_get_api_router(event, url)
            //zp_fetch_api(fetch, page.url, ad.formData)
            */
        }
        catch (error) {
            console.error(error)
        }
        console.log('//handle_form_submit()')
        
    }

</script>

<h1>Send Data</h1>

<form bind:this={xform} on:submit|preventDefault={handle_form_submit}>

<table class="tableCompact">
    <tbody>
        <tr>
            <td class="td33 center">
                <button
                    formaction="?/sendJsonData"
                    name='sendJsonData'
                    value='whooopaa'
                    on:click|preventDefault={handle_send_json}
                >
                    Send JSON 
                    {#await promise_sendJson}
                        sending...
                    {:then tdata} 
                        init/data {tdata}
                    {:catch error} 
                        Error: {error}
                    {/await}
                </button>
                <button                    
                    type="submit"
                    formaction="?/sendFormData"
                    name="sendFormData"
                    value='whooopaa'
                >
                    Send (default)
                    {#await promise_sendForm}
                        sending...
                    {:then tdata} 
                        init/data {tdata}
                    {:catch error} 
                        Error: {error}
                    {/await} 
                </button>
            </td>
            <td width="" class="center">
                    <center>
                        <table class="tableCompact">
                            <tbody>
                                <tr>
                                    <td width="1">name</td>
                                    <td><input class="input" type="text" name="name" bind:value={data.name} placeholder="" /></td>
                                </tr>
                                <tr>
                                    <td>phone</td>
                                    <td><input class="input" type="text" name="phone" bind:value={data.phone} placeholder="" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </center>
            </td>
        </tr>
    </tbody>
</table>
</form>

<table class="tableCompact">
    <tbody>
        <tr>
            <td width="50%">
                <VarDump title="form" vardump={form.formData} />
            </td>
            <td width="50%">
                <VarDump title="data" vardump={data.jsonData} />
            </td>
        </tr>
        <tr>
            <td>
                <pre>{JSON.stringify(form, null, 2)}</pre>
            </td>
            <td class="50%">
                <pre>{JSON.stringify(data, null, 3)}</pre>
            </td>
        </tr>
    </tbody>
</table>