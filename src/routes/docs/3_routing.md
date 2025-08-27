# Routing


## Namespaces (php)
All +server.php files within the /routes directory must declare a unique namespace and be assigned to the variable $zpns. 
This convention allows SveltePHP to properly load your server route files and utilize the same method conventions, such as load().

```php
<?php 
      namespace uniqNameSpace; 
      $zpns = __NAMESPACE__;
```


## +page.server.php
```php
<?php namespace zp111; $zpns=__NAMESPACE__;

      function load() {
            global $data;

            return 'Hello PHP';

            // or

            return [
                  'message' => 'Hello from PHP',
            ];
      }

      function actions($action, $value) {
            global $db, $data;

            // e.g. ?/myAction from SvelteKit.

            switch ($action) {

                  case 'myAction':
                              return "Hello myAction from PHP. received value: $value";
                        break;

                  case 'myDBrequestAction':

                              $result = $db->query("SELECT 'Hello from DB' AS message;");  
                              return $result;

                              // You can use $db for your own DB logic.
                              // When .env.ZEELTEPHP_DATABASEURL is used, ZeeltePHP uses the internal db.PROVIDER.php.

                        break;
            }
	}

      function action_myAction($value) {

            // This 'outside' action-method takes precedence over actions() 

            $result = myLibFunction($value);  // from Shared PHP lib which is pre-loaded. No include() required.

            return $result;
      }

?>
```


## +layout.server.php
```php
<?php namespace zp222; $zpns=__NAMESPACE__;

      function load() {
            global $data;

            return 'Hello Layout-PHP';

            // or

            return [
                  'message' => 'Hello from Layout-PHP',
            ];
      }
?>
```


## +page.js
```js
import { zp_fetch_api } from "zeeltephp";

export async function load({ fetch, url }) {

      const result_php = await zp_fetch_api(fetch, url);

      or 

      const promise_php = zp_fetch_api(fetch, url);

      return [
            message: 'Hello from +page.js',
            result_php,
            promise_php
      ];
}
```


## +page.svelte
```html
<script>
      import { zp_fetch_api } from "zeeltephp";

      export let data;  

      let promise = data?.promise_php || undefined;

      async function handle_click(event) {
            promise = zp_fetch_api(fetch, event)
                  .then(data => {})
                  .catch(error => {})
      }

      async function handle_submit(event) {
            const data = await zp_fetch_ap(fetch, event);
      }
</script>

<button
      type="button"
      formaction="?/myAction"
      value="1"
      on:click={handle_click}
/>

<form on:submit={handle_submit}>
      <button
            type="submit"
            formaction="?/myAction"
            value="42"
      />
      {#await promise}
            ..
      {:then data}
            ..
      {:catch error}
            .. 
      {/await}
</form>
```


## +server.php
```php
<?php namespace zp123; $zpns=__NAMESPACE__;

      // same for POST, PUT, PATCH, DELETE, HEAD
      function GET() {
            global $data;
            return [
                  'message' => 'Hello from +server.php-GET',
            ];
      }
      
      // fallback if none of the others exist 
      function fallback() {
            global $data;
            return [
                  'message' => 'Hello from +server.php-fallbacck',
            ];
      }
?>
```


## +page.svelte
```html
<script>
      import { zp_fetch_api } from "zeeltephp";

      async function handle_submit(event) {
            const data = await zp_fetch_ap(fetch, 'api/apidemo');
         // OR
            const data_load = await zp_fetch_api(fetch, 'api/apidemo/', undefined, 'GET')
      }
</script>

<button
      type="button"
      formaction="?/myAction"
      value="1"
      on:click={handle_click}
/>

<form on:submit={handle_submit}>
      <button
            type="submit"
            formaction="?/myAction"
            value="42"
      />
      {#await promise}
            ..
      {:then data}
            ..
      {:catch error}
            .. 
      {/await}
</form>
```