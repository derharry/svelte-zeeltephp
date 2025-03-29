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

    let vd_zparSvelte
    let vd_zparPHP
    let promise_sendJson;
    let promise_sendForm;


    function handle_json_submit(event) {
        try {
            console.clear();
            console.log('handle_json_submit()');
            vd_zparSvelte = null
            vd_zparPHP    = null

            const zpar = new ZP_ApiRouter(event, submit_data);
            vd_zparSvelte = zpar;

            promise_sendJson = zp_fetch_api(fetch, zpar)
                .then((data) => {
                    console.log(data)
                    vd_zparPHP = data
                })
                .catch((error) => {
                    vd_zparPHP = error
                    //console.error(error);
                })
            //promise_sendForm
        } catch (error) {
            vd_zparPHP = error
            console.error(error);
        }
        form = form;
    }

    function handle_form_submit(event) {
        try {
            console.clear();
            console.log('handle_form_submit()');
            vd_zparSvelte = null
            vd_zparPHP = null
            vd_zparSvelte = new ZP_ApiRouter(event);

            promise_sendForm = zp_fetch_api(fetch, event)
                .then(data => {
                    console.log(data)
                    vd_zparPHP = data
                })
                .catch(error => {
                    vd_zparPHP = error
                    //console.error(error);
                })
            //promise_sendForm
        } catch (error) {
            vd_zparPHP = error
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
                <VarDump title="ZP_ApiRouter (Svelte)" vardump={vd_zparSvelte} />
            </td>
            <td width="50%">
                <VarDump title="ZP_ApiRouter (PHP/data)" vardump={vd_zparPHP} />
            </td>
        </tr>
        <!--
        <tr>
            <td>
                <pre>{JSON.stringify(vd_zparSvelte, null, 2)}</pre>
            </td>
            <td class="50%">
                <pre>{JSON.stringify(vd_zparPHP, null, 3)}</pre>
            </td>
        </tr>
        -->
    </tbody>
</table>