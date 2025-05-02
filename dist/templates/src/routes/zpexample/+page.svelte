<script>

    import { page } from '$app/state';
    import { onMount } from 'svelte';
    import { zp_fetch_api } from 'zeeltephp';

    export let data;

    let promise_load;

    onMount(() => {
        console.log(' >> +page.svelte/onMount');
        console.log('  @ export let data (from +page.js)');
        console.log(data);

        console.log('  @ data from onMount/zp_fetch_api (from +page.svelte)');
        promise_load = zp_fetch_api(fetch)
            .then((data) => {
                console.log(data);
            })
            .catch((error) => {
                console.error(error);
            })

    })

    function handle_form_submit(event) {
        promise_load = zp_fetch_api(fetch, event)
    }

    function handle_custom_submit(event) {
        //roadmap
    }

    function handle_button_click(event) {
        //roadmap
    }

</script>

<h1>Welcome to ZeeltePHP-SvelteKit Demo</h1>
<p>Visit <a href="https://github.com/derharry/svelte-zeeltephp">https://github.com/derharry/svelte-zeeltephp</a> to read the documentation</p>


Promise:
{#await promise_load}
    loading...
{:then data} 
    data
{:catch error}
    ohoh
{/await}

