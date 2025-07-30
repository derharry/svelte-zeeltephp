<?php

/**
 * loads the exec file for the given routeType
 *
 * @param string $routeBase       path to the file
 * @param string $routeFile       filename
 */
function zp_executor(string $routeBase, string $routeFile)
{
    global $zpAR, $zpTime, $data;
    zp_log_debug("zp_executor()");
    zp_log_debug("@ $routeBase", 3);
    zp_log_debug("@ $routeFile", 3);
    $zpTime->start('zp_executor()');

    [$fqdnFile, $fqdn] = zp_executor_GetFQDNfile($routeBase, $routeFile);
    $fqns = 'ZeeltePhp\\Runtime\\zp' . $fqdn;

    zp_log_debug("fqdnFile $fqdnFile", 3);
    zp_log_debug("fqdn     $fqdn", 3);
    zp_log_debug("fqns     $fqns", 3);

    if (is_string($fqdnFile) && is_file($fqdnFile)) {
        include_once($fqdnFile);

        if ($zpAR->context == 'api') {
            include_once('core/exec.server.php');
            $data = zp_exec_PlusServerPHPFile($fqns);
        }
        else {
            // context == 'page'
            // if fileNane contains 'layout' -> load
            // if fileName contains 'page' -> ..
            if (str_contains($routeFile, '+layout.server.')) {
                include_once('core/exec.layout.server.php');
                $data = zp_exec_PlusLayoutServerPHPFile($fqns);
            }
            else if (str_contains($routeFile, '+page.server.')) {
                include_once('core/exec.page.server.php');
                $data = zp_exec_PlusPageServerPHPFile($fqns);
            }
        }
    }

    zp_log_debug($zpTime->endN('zp_executor()'), 1);
    zp_log_debug("/zp_executor()");
    return $data;
}


function zp_executor_GetFQDNfile($routeBase, $routeFile) {
    $routeType = null; // +[page.server|layout.server|server)
    $fqdnFile = null;
    $fqdn = null;
    $orgFile = null;
    $orgFqdn = null;
    try {
        zp_log_debug("GetFQDNfile()", 2);
        zp_log_debug("    @ routeBase " . $routeBase);
        zp_log_debug("    @ routeFile " . $routeFile);
        
        // in prod receive the FQDNfile from ZP_ApiRouter/collectRouteFiles
        if (preg_match('#(\+.*server)\.([0-9]+)\.php$#i', $routeFile, $matches)) {
            zp_log_debug("    -- match prod $routeFile");
            $routeType = $matches[1] ?? null;
            $fqdnFile  = "$routeBase/$routeFile";
            $fqdn      = $matches[2] ?? null;
        }
        // in dev we only get the "OriginalFile/DevelopmentFile"
        elseif (!$fqdn && preg_match('#(\+.*server)\.php$#i', $routeFile, $matches)) {
            zp_log_debug("    -- match dev $routeFile");
            $routeType = $matches[1] ?? null;
            $orgFile   = "$routeBase/$routeFile";
            $orgFile   = realpath($orgFile);
            $orgFqdn   = filemtime($orgFile);
        }

        // in prod receive the FQDNfile from ZP_ApiRouter/collectRouteFiles
        // but a new Original file could exist (1st time run or after new FTP-upload)
        //  -  PATH of FQDNfiles = $routeBase in PATH_ZPROUTES
        //  -  PATH of OrgFile   = in $routeBase
        // A) in builds 
        //       the route contains the newer OriginalFile and 
        //       needs to be replaced with FQDNfile (from 1st run)
        // B) after export of build 
        //       the new OriginalFile could exist and this
        //       FQDNfile needs to be replaced with new FQDNfile
        // C) delete OriginalFile
        if (ZP_ENV == 'production') {
            $orgFile  = "$routeBase/$routeType.php";
            zp_log_debug("    -- FQDN prod orgFile $orgFile");
            if (is_file($orgFile)) {
                [$fqdnFile, $fqdn] = zp_executor_CreateFQDNfile($routeBase, $routeType, $orgFile);
                if ($fqdnFile && is_file($fqdnFile)) unlink($orgFile);
            };
        }
        // in dev we only have the "OriginalFile/DevelopmentFile"
        //   PATH of FQDNfiles = PATH_ZPTMP
        //   PATH of OrgFile   = PATH_ZPROUTES and set in $orgFile already
        //   FQDN is file-last-modified-time of OriginalFile
        // A) the FQDNfile could be already generated from current FQDN
        // B) the FQDN could be newer and the FQDNfile needs to be replaced with the new FQDNfile
        else {
            // (ZP_ENV == 'development|'self-development')
            $orgFile       = @str_replace('\\', '/', $orgFile);
            $routeBase     = @str_replace("$routeType.php", '', $orgFile);
            $routeBaseFQDN = @str_replace(PATH_ZPROUTES, PATH_ZPTMP, $routeBase);

            zp_log_debug("    -- FQDN orgFile   $orgFile");
            zp_log_debug("     @ routeType      $routeType");
            zp_log_debug("     @ routeBase      $routeBase");
            zp_log_debug("     @ routeBaseFQDN  $routeBaseFQDN");

            // use PATH_ZPTMP for generated PATH_ZPROUTES
            if (!is_dir(PATH_ZPTMP)) mkdir(PATH_ZPTMP);
            if (is_dir($routeBaseFQDN)) {
                $FQDNfiles = zp_scandir($routeBaseFQDN, "#".preg_quote($routeType)."#");
                if (sizeof($FQDNfiles) >= 1) {
                    if (preg_match('#\+.*server\.([0-9]+)\.php$#i', $FQDNfiles[0], $matches)) {
                        $fqdnFile = "$routeBaseFQDN/".$matches[0];
                        $fqdn     = $matches[1] ?? null;
            }}}

            zp_log_debug("     @ orgFqdn   $orgFqdn");
            zp_log_debug("     @ fqdn      $fqdn");
            zp_log_debug("     @ fqdnFile  $fqdnFile");

            if (!$fqdn || !$fqdnFile || ($fqdn != $orgFqdn)) {
                $oldFile  = $fqdnFile;
                [$fqdnFile, $fqdn] = zp_executor_CreateFQDNfile($routeBaseFQDN, $routeType, $orgFile);
                zp_log_debug("     @ fqdn      $fqdn");
                zp_log_debug("     @ fqdnFile  $fqdnFile");
                zp_log_debug("     @ oldFile   $oldFile");
                // delete OldFQDNfile
                if ($fqdnFile && $oldFile && is_file($oldFile)) unlink($oldFile);
            }
        }
    } 
    catch (Throwable $error) {
        zp_handle_error($error); //, "Throwable");
    } 
    finally {
        zp_log_debug("/GetFQDNfile()", 2);
        return [ $fqdnFile, $fqdn ];
    }
}

function zp_executor_CreateFQDNfile($toRouteBase, $routeType, $fromOrgFile) {
    global $zpAR;
    $fqdnFile = '';
    $fqdn     = '';
    try {
        zp_log_debug("CreateFQDNfile()", 2);
        zp_log_debug("    @ toRouteBase " . $toRouteBase);
        zp_log_debug("    @ routeType   " . $routeType);
        zp_log_debug("    @ fromOrgFile " . $fromOrgFile);

        // check if toRouteBase including zpAR->route path exist
        if (!is_dir($toRouteBase)) mkdir($toRouteBase, 0777, true);

        // prepare fqdnFile-name and source fqdnSource
        $fqdn     = filemtime($fromOrgFile);                         # is new FQDN-id
        $fqns     = 'ZeeltePhp\\Runtime\\zp' . $fqdn;                # is new Namespace
        $fqdnFile = "$toRouteBase/$routeType.$fqdn.php";             # is fqdnFile with path
        $source   = file_get_contents($fromOrgFile);                 # is orgSource
        $source   = trim(str_replace(['<?php', '?>'], '', $source)); # is orgSource cleaned
        $source   = "namespace " . $fqns . ";\n" . $source;            # is fqdnSource

        // write new FQDNfile
        file_put_contents($fqdnFile, "<?php $source\n?>");  # fqdnFile
        zp_log_debug("    @ fqdn      " . $fqdn);
        zp_log_debug("    @ fqnFile   " . $fqdnFile);
    } 
    catch (Throwable $error) {
        zp_handle_error($error); //, "Throwable");
    } 
    finally {
        zp_log_debug("/CreateFQDNfile()", 2);
        return [ $fqdnFile, $fqdn ];
    }
}


?>