<script>
    import { zp_fetch_api, zp_fetch_api_event_action } from "$lib/zeeltephp/zeeltephp.api";
    import { get_event_action_details } from "$lib/zeeltephp/class.zp.eventdetails"
    import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
    import { onMount } from "svelte";
    import TableShowData from "$lib/TableShowData.svelte";

    let form = {
        send    : false,
        error   : "",
        message : "",
    }

    let data = {
        name    : "",
        phone   : ""
    }
    data.name    = crypto.randomUUID()
    data.phone   = crypto.randomUUID()

    let xform;
    let promise_sendJson = undefined
    let promise_sendForm = undefined

    onMount(() => {
        // xform.submit();
    })

    function handle_send_json(event) {
        try {
            //const ed = get_event_action_details(event);
            const zpar  = new ZP_ApiRouter(event, data)
            zpar.dump();
            promise_sendJson = zp_fetch_api(fetch, zpar, data)
                .then((data1) => {
                    if (data?.ok) form.ok = data.ok
                    data = data1
                    form = form;
                })
                .catch((error) => console.error(error))
        }
        catch (error) {
            console.error(error)
        }
    }

    async function handle_form_submit(event) {
        const debug = false
        try {

            const zpar = new ZP_ApiRouter(event)
            zpar.prepare()
            //console.log(zpar.fetch_options);


            if (debug) console.log('***********************')
            if (debug) console.log(zpar.fetch_url)
            if (debug) console.log(zpar.fetch_options);
            /*
            const xxx = fetch(zpar.fetch_url, zpar.fetch_options)
                .then(response => response.json())
                .then((data) =>{ console.log('data1', data); })
                .catch((error) => console.error(error))
            console.log('***********************')
            */
            promise_sendForm = zp_fetch_api(fetch, zpar, form)
                .then((data1) => {
                    if (data?.ok) form.ok = data.ok
                    data = data1
                    form = form;
                })
                .catch((error) => console.error(error))
            if (debug)  console.log('***********************')
            

            //const zp = zp_get_api_router(event, url)
            //zp_fetch_api(fetch, page.url, ad.formData)
            
        }
        catch (error) {
            console.error(error)
        }
    }

</script>



<TableShowData title="Send JSON example">
    <tr>
        <td width="50%" class="center">
            <button
                formaction="?/yeah"
                name='yeah'
                value='whoopaa'
                on:click|preventDefault={handle_send_json}
            >Send (JSON)</button>
        </td>
        <td width="50%">
            <pre>x</pre>
        </td>
    </tr>
</TableShowData>



<TableShowData title="Sendform example">
    <tr>
        <td width="50%">
                <form bind:this={xform} on:submit|preventDefault={handle_form_submit}>
                    <center>
                        <table class="tableCompact">
                            <tbody>
                                <tr>
                                    <td>name</td>
                                    <td><input class="input" type="text" name="name" bind:value={data.name} placeholder="" /></td>
                                </tr>
                                <tr>
                                    <td>phone</td>
                                    <td><input class="input" type="text" name="phone" bind:value={data.phone} placeholder="" /></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <button
                                            type="submit"
                                            formaction="?/sendform"
                                            name="sendform"
                                        >Send (default) </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </center>
                </form>
        </td>
        <td width="50%">

            <p>Form state: 
                {#await promise_sendForm}
                    sending...
                {:then tdata} 
                    {#if form.send}
                        send (finished)
                    {:else}
                        not send
                    {/if}
                {:catch error} 
                    Error: {error}
                {/await}
            </p>
            <pre class="wrap">{JSON.stringify(form)}</pre>
        </td>
    </tr>
</TableShowData>






