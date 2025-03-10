<script>
    import { zp_fetch_api, zp_fetch_api_event_action, zp_get_api_router } from "$lib/zeeltephp/zeeltephp.api";
    import { get_event_action_details } from "$lib/zeeltephp/event.helper"
	import { page } from '$app/state';
    import { ZP_ApiRouter } from "$lib/zeeltephp/class.zp.apirouter";

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
            
            zp_fetch_api_event_action(fetch, page.url, event)
            console.log(ad)

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

    <form action="hello.php" on:submit={handle_form_submit}>


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
