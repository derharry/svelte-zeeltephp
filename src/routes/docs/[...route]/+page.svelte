<script>
    import { dev } from '$app/environment'
    import { Marked } from "zeelte/ui"
    import { LucideIcon } from "zeelte/ui"
    import { EventDetails } from "zeeltephp"

    let {
        data,
        form
    } = $props()

    let editMode = $state(false)

    $effect(() => {
        if (form && form?.newMarkdown) {
            //data.markdown = form.newMarkdown
        }
    })

    function onSubmitForm(e) {
    }

</script>

{#if dev}

    <div class="editFrame">
        
        <button 
            formaction="?/saveChanges"
            onclick={() => editMode = !editMode}
            class="editFrame-button"
        ><LucideIcon name="pencil" /></button>

        {#if editMode}

            <form method="POST" onsubmit={(e) => onSubmitForm(e)}>
                <button 
                    type="submit"
                    formaction="?/saveChanges"
                    class="saveChanges-button"
                ><LucideIcon name="save" /></button>
                <textarea name="newMarkdown">{data.markdown}</textarea>
            </form>

        {:else}

            <Marked markdown={data.markdown}/>

        {/if}

    </div>

{:else if !data?.markdown}

    no-text

{:else}

    <Marked markdown={data.markdown}/>

{/if}


<style>

    .actionBar {
        margin:  1em;
        padding: 0.75em 1em;
        background-color: black
    }

    .saveChanges-button,
    .editFrame-button {
        position: absolute; 
        top:      1em;
        right:    1.5em;
        border-radius:    1em;
        background-color: none;
    }
    .saveChanges-button {
        right: 5em
    }

    form,
    textarea {
        width:  100%;
        height: 100%;
    }

    .editFrame {
        position: relative; 
        height:   100%
    }

</style>
