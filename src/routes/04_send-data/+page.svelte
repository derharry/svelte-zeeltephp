<script>
    import VarDump from "$lib/VarDump.svelte";

    // debug imports
    import { ZP_EventDetails } from "$lib/zeeltephp/class.zp.eventdetails";
    import { ZP_ApiRouter } from '$lib/zeeltephp/class.zp.apirouter';
    import { zp_fetch_api } from "$lib/zeeltephp/zp.fetch.api";


    let form = {
        error    : "",
        message  : "", 
        debug    : null,
        isSend   : false,
        jsonData : false,
        formData : false,
    }

    let submit_data = {
        name    : "",
        phone   : ""
    }
    submit_data.name    = crypto.randomUUID()
    submit_data.phone   = crypto.randomUUID()

    let xform;
    let promise_sendJson;
    let promise_sendForm;

    function handle_json_submit(event) {
        try {
            console.clear();
            console.log('handle_json_submit()');
        } catch (error) {
            console.error(error);
        }
    }

    function handle_form_submit(event) {
        try {
            console.clear();
            console.log('handle_form_submit()');

            //const ed = new ZP_EventDetails(event)
            //ed.dump()
            //form.debug = ed;

            const za = new ZP_ApiRouter(event)
            za.dump()
            form.debug = za;

            promise_sendForm = zp_fetch_api(fetch, event)
                .then(data => {
                    console.log(data)
                    form.jsonData = data
                })
                .catch(error => {
                    console.log(error);
                })

        } catch (error) {
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
                <VarDump title="form" vardump={form} />
            </td>
            <td width="50%">
                <VarDump title="data" vardump={form.jsonData} />
            </td>
        </tr>
        <!--
        <tr>
            <td>
                <pre>{JSON.stringify(form, null, 2)}</pre>
            </td>
            <td class="50%">
                <pre>{JSON.stringify(submit_data, null, 3)}</pre>
            </td>
        </tr>
        -->
    </tbody>
</table>