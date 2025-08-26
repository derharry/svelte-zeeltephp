<script>

    // GitHub repo: https://github.com/metonym/svelte-highlight
    import github  from "svelte-highlight/styles/github";
    //import 'svelte-highlight/styles/monokai-sublime.css';   // Monokay-sublime-style
    import { HighlightSvelte } from "svelte-highlight";
    import javascript from 'svelte-highlight/languages/javascript';
    import php        from 'svelte-highlight/languages/php';

    let {
        language = 'php',
        code = 'hi',
        ...rest
    } = $props()

    // Map language string to actual language definition object
    const languageMap = {
        javascript,
        php,
        js:         javascript,
        svelte:     javascript
    };

    let languageDefinition = languageMap[language] ?? javascript;

    function getStripped(value = '') {
        if (value === null) return value
        return value.replaceAll('                     ', '')
    }

    function getStrippedAuto(value) {
        if (!value) return '';
        const lines  = value.split('\n');
        let indent = Math.min(
            ...lines
                .slice(1)                                   // ignore first row
                .filter(line => line.trim().length > 0)     // only non empty lines
                .map(line => line.match(/^\s* /)[0].length)  // get length
        );
        indent = Number.isFinite(indent) ? indent : 0; // fallback undefinded/Infinity
        const re = new RegExp(`^\\s{0,${indent}}`);
        return lines.map(line => line.replace(re, '')).join('\n')
    }

</script>

<svelte:head>
    {@html github}


    <style>
        /* Kill any background the theme applies */
        .hl_js,
        .hl_js * {
            background: none !important;
        }

        /* Strip the box/container theme styles */
        .hl_js {
            background: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            font-family: inherit !important;
            font-size: inherit !important;
        }
    </style>

</svelte:head>


<HighlightSvelte 
    language={languageDefinition} 
    code={getStrippedAuto(code)} 
/>