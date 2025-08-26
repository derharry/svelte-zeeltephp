<script>
</script>

<h1>ZeeltePHP (aka SveltePHP)</h1>

<h6>Introduction</h6>
<p>
    ZeeltePHP is designed as plugin for projects build with SvelteKit-Adapter-Static, that 
    enables PHP backend integration using SvelteKit-style file conventions 
    (e.g., +page.server.php, +layout.server.php, +server.php).
</p>

<ul>
   <li>Use +server.php files in your project just like SvelteKit’s +server.js|ts.</li>
   <li>Develop as if you’re using SvelteKit natively, including hot-reloading.</li>
   <li>Easily port existing PHP projects into the SvelteKit project structure, or move SvelteKit-native projects to a PHP backend.</li>
</ul>
<ul>
    <li>use SvelteKit SSR +.server.js|js as +.server.php</li>
    <li>The builds run on static-hosting environments like Apache, Nginx, PHP, MySQL, Wordpress, etc.</li>
</ul>
<p>It tries to mimic SvelteKit in the backend, by having structured PHP-code in 
        SvelteKits path-routing and could be ported a later timestip, or keep-it-running as static.</p>


<h6>In Short</h6>
<ul>
    <li>Run `npm run dev` and use `+.php` files inside your `/src/routes`.</li>
    <li>Run `npm run build` and run and deploy in static environment. Access your app at http://domain/<build>.</li>
</ul>



<p>
    The itention is to develop SvelteKit projects for following Use-Cases:
</p>
<ul>
    <li>
        SvelteKit only
        <ul>
            <li>front- and back-end JavaScript</li>
            <li>for newer projects developed SvelteKit only and using +.server.js|ts SSR</li>
            <li>builds run in NodeJS environment with SvelteKit</li>
        </ul>
    </li>

    <li>
        Adapter-Static
        <ul>
            <li>front-end SvelteKit, backend static with PHP</li>
            <li>to developed projects with Adapter-Static for static-environments, by having 
                SvelteKits file-conventions of +.server.php in /src/routes. 
            </li>
            <li>builds run in static-hosting environment with PHP</li>
            <li>for projects needing a modern Front-End, but have legacy PHP-code in backend.</li>
            <li>legacy-PHP-code can be developed to only data returning like it would be within .js|.ts</li>
            <li>have code-base in a ready-to-port to SvelteKit-only at a later time-stip, or keep it running.</li>
        </ul>
    </li>

    <li>
        Mixed: SvelteKit + Adapter-Static
        <ul>
            <li>front- and back-end in both SveleKit SSR + PHP SSR</li>
            <li>to develop projects co-existing of SvelteKit and PHP</li>
            <li>builds run in mixed NodeJS- + static-environment</li>
            <li>a route can be either SSR with SvelteKit or PHP</li>
            <li>have code-base in a ready-to-full-port either way: php to js|ts, or js|ts to php</li>
        </ul>
    </li>
</ul>

