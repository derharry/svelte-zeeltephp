<script>
</script>


<div class="input-row">
     <button
          type="submit"
          name="btnDBcheck"
          formaction="?/DB_get"
     >Get DB</button>
     <button
          type="submit"
          name="btnDBsqlst"
          formaction="?/DB_execSQL"
     >exec SQL</button>
</div>
<textarea name="sqlstatement">SELECT 'Hello,  World!' AS message;</textarea>