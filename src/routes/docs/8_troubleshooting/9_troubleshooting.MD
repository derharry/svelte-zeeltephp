# Troubleshooting

## ZP Dev
The `/zpdev/` folder serves as a complete demo and debugger. 
<br>Copy it into your `routes` and access it at `http://localhost/myZPproject/zpdev`.
<br>If `zpdev` does not work, there may be a misconfiguration from the installation steps. 

See troubleshooting for more details.
* Use <ZPDev /> to inspect and interact with your API routes directly from the browser. 
* The load() and actions() should return all globals for best debugging results.
* You can test both GET and POST actions, as well as custom action handlers.
* PHP Errors will be shown in <ZPDev /> directly from the browser, but 
  you can also access http://localhost/myZPproject/static/api 

## Troubleshooting
If you encounter issues with your route, load(), or actions(), you can debug your setup by including `<ZPDev />` in your `+page.svelte`.
<br>This enables you to use the `+page.server.php` `+server.php` `+layout.server.php` directly to execute load(), actions(), edit action names/values, and send test data as either FormData or JSON.
<br>Make sure the global variables $zpAR, $env, and $db are accessible and exposed.
<br>
<br>**<ins>Attention!</ins>** 
<br>Don't forget to remove `<ZPDev />` and exposed globals before deploying to production to avoid exposing debug tools and sensitive data.
<br>

**`+page.svelte` or in a Component.**
```svelte
<script>
     import { ZPDev } from "zeeltephp";
</script>
<ZPDev />
```

**+page.server.php**
```php
<?php

      function load() {
            // load(), actions()
            global $zpAR, $env, $db;
            return [
                  'zpAR'  => $zpAR, // expose ApiRouter of PHP
                  'zpEnv' => $env,  // expose your exported .env
                  'zpDB'  => $db    // export your DB or ZP-Database-provider connection
            ];
      }
?>
```