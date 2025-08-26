<script>
     import { ZPDev } from "zeeltephp";
     let { data, form } = $props();
</script>

<h1>ZPDev</h1>
<ZPDev />

<pre>
&lt;script&gt;
     import &lbrace; ZPDev &rbrace; from 'zeeltephp'
&lt;/script&gt;
&lt;ZPDev /&gt;
</pre>
<p>
     The ZPDev Component can be used as a debugger in your Svelte Components.
     <br>It helps to analyze the interaction with and the response from ZeeltePHP from the current route.
</p>
<p>
     The route ./zpdev comes with +page.js, +page.server.php, +server.php and +page.svelte (this page).
     <br>ZPDev initial values works directly with the +files which helps to see if the environment is set-up correctly,
     and actions (Events of button, form, ...), load() at navigation, etc works properly.
     <br>The values can be changed to like the action-names you use in your Components to test the request with sample data.
</p>
<ul>
     <li>The Component exists of the header with "Apps" and "Dumps"</li>
     <li>and with 2 panels showing the current "App" and the dump for the "Dumps"</li>
     <li>"Apps"
          <ul>
               <li>+page.server.php
                    <br>test load(), actions(), form-Events on +page.server.php
               </li>
               <li>+server.php
                    <br>test GET, POST, PUT, UPDATE, DELETE, HEAD on +server.php
               </li>
               <li>DB
                    <br>is related to action_DB_get() and action_DB_execSQL() in +page.server.php to test global $db or if .env.ZEELTEPHP_DATABASE_URL is configured.
               </li>
          </ul>
     </li>
     <li>"Dumps" show the data or the exposed instances in the response via the keys:
          <ul>
               <li>data, form, error
                    <br>are the Stores where zp_fetch() resolve the response to.
               </li>
               <li>ED js, ED sv
                    <br>js:  _zpED_js: expose instance of EventDetails from +page.js
                    <br>sv:  _zpED_sv: expose instance of EventDetails from Svelte Component
               </li>
               <li>AR js, AR sv, AR php
                    <br>js:  _zpAR_js: expose instance of ZP_ApiRouter from +page.js
                    <br>sv:  _zpAR_sv: expose instance of ZP_ApiRouter from Svelte Component
                    <br>php: _zpAR:    expose instance of ZP_ApiRouter from PHP.
               </li>
               <li>DB, ENV (php only)
                    <br>_zpAR: expose instance of $db
                    <br>_zpDB: expost instance of $env (the .env-file)
               </li>
          </ul>
</ul>