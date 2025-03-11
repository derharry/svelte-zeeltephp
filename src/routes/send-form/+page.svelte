<script>
    import { zp_fetch_api, zp_fetch_api_event_action } from "$lib/zeeltephp/zeeltephp.api";
    import { get_event_action_details } from "$lib/zeeltephp/event.helper"
    import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";
    import { onMount } from "svelte";

    let form = {
        ok      : false,
        error   : "",
        name    : "",
        phone   : "",
        email   : "",
        message : "",
    }

    async function handle_form_submit(event) {
        try {
            console.clear();
            event.preventDefault();

            const zpar = new ZP_ApiRouter()
            zpar.action = 'goGo'
            zpar.value  = '69'
            zpar.data   = form;
            zpar.prepare()
            //console.log(zpar.fetch_options);


            console.log('***********************')
            console.log(zpar.fetch_url)
            console.log(zpar.fetch_options);
            /*
            const xxx = fetch(zpar.fetch_url, zpar.fetch_options)
                .then(response => response.json())
                .then((data) =>{ console.log('data1', data); })
                .catch((error) => console.error(error))
            console.log('***********************')
            */
            const response = zp_fetch_api(fetch, zpar, event)
                .then((data) => console.log('data2', data))
                .catch((error) => console.error(error))
            console.log('***********************')
            

            //const zp = zp_get_api_router(event, url)
            //zp_fetch_api(fetch, page.url, ad.formData)
            
        }
        catch (error) {
            console.error(error)
        }
    }



    //$: console.log(' data', data)

    // development
    form.name    = crypto.randomUUID()
    form.phone   = crypto.randomUUID()
    form.email   = crypto.randomUUID()
    form.message = crypto.randomUUID()

    let xform;

    onMount(() => {
       // xform.submit();
    })

</script>

<h1 class="h4 mb-5">Kontakt</h1>
<p class="mb-5">Graag kunt u ons voor alle balangen, vraagen of andere aanmerkingenen kontakteeren.</p>


<button
    type="submit"
    formaction="?/sendform"
    name="sendform"
>
    XXXXX
</button>


{#if form?.ok}

        <p>Uw kontakt-formulier is ingestuurd.</p>

{:else}

    <form bind:this={xform} on:submit={handle_form_submit}>


        <center>
            <button
                type="submit"
                formaction="?/sendform"
                name="sendform"
            >
                <i class="fa fa-send"></i>
                Verstuuren
            </button>
            <table>
                <tbody>
                    <tr>
                        <td>Naam</td>
                        <td><input class="input" type="text" name="name" bind:value={form.name} placeholder="" /></td>
                    </tr>
                    <tr>
                        <td>Tel/Mobile</td>
                        <td><input class="input" type="text" name="phone" bind:value={form.phone} placeholder="" /></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><input class="input" type="text" name="email" bind:value={form.email} placeholder="" /></td>

                    </tr>
                    <tr>
                        <td>Text</td>
                        <td><textarea class="textarea" rows="4" placeholder="" name="message" bind:value={form.message} ></textarea></td>
                    </tr>
                </tbody>
            </table>
        </center>
    </form>

{/if}
