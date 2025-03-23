<script>
    import VarDump from "$lib/VarDump.svelte";

    // debug imports
    import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
    import { ZP_ApiRouter } from '$lib/zeeltephp/class.zp.apirouter';
    import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";


    let form = {
        error    : "",
        message  : "", 
        isSend   : false,
        sendData : null,
        receivedData : null,
    }

    let submit_data = {
        name    : "",
        phone   : ""
    }
    submit_data.name    = crypto.randomUUID()
    submit_data.phone   = crypto.randomUUID()

    let xform;

    let vardump_ApiRouterSvelte
    let vardump_ApiRouterPHP
    let promise_sendJson;
    let promise_sendForm;

    async function handle_json_submit_debug(event) {
        try {
            console.clear();
            console.log('handle_json_submit()');

            const zpar = new ZP_ApiRouter(event, submit_data);
            //zpar.dump();
            console.log(' ** ', zpar.fetch_url)
            //console.log(' ** ', zpar.fetch_options)
            vardump_ApiRouterSvelte = null
            vardump_ApiRouterPHP = null

            //promise_sendJson = zp_fetch_api()
            
            //OK :-)
            /*

            console.log(' body pre  ', zpar.fetch_options.body);
            zpar.fetch_options.body = JSON.stringify({
                zp_route: zpar.route,
                zp_action: zpar.action,
            })
            */
            console.log(' body post ', zpar.fetch_options.body);
            vardump_ApiRouterSvelte = zpar;


            //promise_sendJson = fetch(zpar.fetch_url, zpar.fetch_options)
            //promise_sendJson = zp_fetch_api(fetch, zpar) 
            promise_sendJson = zp_fetch_api(fetch, event, submit_data) 
                .then(data => {
                    console.log('DATA', data)
                    vardump_ApiRouterPHP = data
                })
                .catch(error => {
                    console.error(error)
                    vardump_ApiRouterPHP = error
                })
                

            /*
            //const response = await fetch('http://localhost/sv-zeeltephp/static/api/', {
            const response = await fetch(zpar.fetch_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ zp_route: zpar.route })
            });
            */
        } catch (error) {
            console.error(error);
            vardump_ApiRouterPHP = error
        }
    }

    function handle_json_submit(event) {
        try {
            console.clear();
            console.log('handle_json_submit()');
            vardump_ApiRouterSvelte = null
            vardump_ApiRouterPHP = null
            vardump_ApiRouterSvelte = new ZP_ApiRouter(event, submit_data);

            promise_sendJson = zp_fetch_api(fetch, event, submit_data)
                .then(data => {
                    console.log(data)
                    vardump_ApiRouterPHP = data
                })
                .catch(error => {
                    vardump_ApiRouterPHP = error
                    console.error(error);
                })
            //promise_sendForm
        } catch (error) {
            vardump_ApiRouterPHP = error
            console.error(error);
        }
        form = form;
    }

    function handle_form_submit(event) {
        try {
            console.clear();
            console.log('handle_form_submit()');
            vardump_ApiRouterSvelte = null
            vardump_ApiRouterPHP = null
            vardump_ApiRouterSvelte = new ZP_ApiRouter(event);

            promise_sendForm = zp_fetch_api(fetch, event)
                .then(data => {
                    console.log(data)
                    vardump_ApiRouterPHP = data
                })
                .catch(error => {
                    vardump_ApiRouterPHP = error
                    console.error(error);
                })
            //promise_sendForm
        } catch (error) {
            vardump_ApiRouterPHP = error
            console.error(error);
        }
        form = form;
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
                                on:click|preventDefault={handle_json_submit}
                        >
                                Send JSON 
                                -{#await promise_sendJson}
                                    sending...
                                {:then tdata} 
                                    init/data {tdata}
                                {:catch error} 
                                    Error: {error}
                                {/await}
                        </button>
                        <br>
                        <button
                                formaction="?/sendFormData"
                                name='sendFormData'
                        >
                                Send FORM 
                                -{#await promise_sendJson}
                                    sending...
                                {:then tdata} 
                                    init/data {tdata}
                                {:catch error} 
                                    Error: {error}
                                {/await}
                        </button>
                </td>
                <td>
                        <table class="tableCompact">
                            <tbody>
                                <tr>
                                    <td width="1">name</td>
                                    <td><input class="input" type="text" name="name" bind:value={submit_data.name} placeholder="" /></td>
                                </tr>
                                <tr>
                                    <td>phone</td>
                                    <td><input class="input" type="text" name="phone" bind:value={submit_data.phone} placeholder="" /></td>
                                </tr>
                            </tbody>
                        </table>
                </td>
            </tr>
        </tbody>
    </table>
</form>


<table class="tableCompact">
    <tbody>
        <tr>
            <td width="50%">
                <VarDump title="ZP_ApiRouter (Svelte)" vardump={vardump_ApiRouterSvelte} />
            </td>
            <td width="50%">
                <VarDump title="ZP_ApiRouter (PHP/data)" vardump={vardump_ApiRouterPHP} />
            </td>
        </tr>
        <!--
        <tr>
            <td>
                <pre>{JSON.stringify(vardump_ApiRouterSvelte, null, 2)}</pre>
            </td>
            <td class="50%">
                <pre>{JSON.stringify(vardump_ApiRouterPHP, null, 3)}</pre>
            </td>
        </tr>
        -->
    </tbody>
</table>