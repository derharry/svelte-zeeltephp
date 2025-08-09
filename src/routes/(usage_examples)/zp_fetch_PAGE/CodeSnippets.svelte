<script>

     import CodeHighlight from '$lib/zeelte/CodeHighlight/CodeHighlight.svelte';

     let {
          code_snippets,
     } = $props()

     let showSvelteVersion = $state(5);
     let showJsPHP         = $state('js');

</script>

<div class="grid-container">
    <div class="header">
        Description
    </div>
    <div class="header">
        Svelte(Kit)
        <br>
        <span class="toggle">
            <button
                class:active={showSvelteVersion == 4}
                onclick={() => showSvelteVersion = 4}
            >v4</button>
            <button
                class:active={showSvelteVersion == 5}
                onclick={() => showSvelteVersion = 5}
            >v5</button>
        </span>
    </div>
    <div class="header">
        ZeeltePHP
        <!--
        <span class="toggle">
            <button
                class:active={showJsPHP == 'js'}
                onclick={() => showJsPHP = 'js'}
            >Svelte</button>
            <button
                class:active={showJsPHP == 'php'}
                onclick={() => showJsPHP = 'php'}
            >PHP</button>
        </span>
        -->
    </div>

    {#each code_snippets as cs}
        <div class="row-title">{cs.title}</div>
        <div class="cell desc">{cs.description}</div>
        <div class="cell skit">
            <CodeHighlight language="svelte" code={showSvelteVersion == 4 ? cs.svelteKit4 : cs.svelteKit5 ? cs.svelteKit5 : cs.svelteKit4 } />
        </div>
        <div class="cell skit">
            {#if cs?.zeelteJS}
                <CodeHighlight language="php" code={cs.zeelteJS  ?? ''}  />
            {/if}
            {#if cs?.zeeltePHP}
                <CodeHighlight language="php" code={cs.zeeltePHP  ?? ''}  />
            {/if}
        </div>
    {/each}
</div>


<style>

    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr; /* Title, SvelteKit, ZeeltePHP */
        border: 0px solid #ccc; 
        border-radius: 0px;
        overflow: hidden;
    }

    .header {
        font-weight: bold;
        padding:     8px;
        padding-bottom: 1em;
    }

    .row-title {
        grid-column: span 3;
        font-weight: bold;
        padding: 0.5em 1em;
        border-bottom: 1px solid;
    }

    /* Special styling for preformatted code cells */
    div.cell {
        padding: 8px;
    }

    div.desc {
        white-space: pre-line;
    }
    /** source colors for JS */
    .skit {}
    /** source colors for PHP */
    .sphp {}

    .toggle.button {
        padding: 0px !important;
        margin:  0px !important;
        background: none !important;
    }
    .toggle.button {
        background: none !important;
    }

</style>
