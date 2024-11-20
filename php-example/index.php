<?php
/**
 * ____________________________________________________________________________
 * 
 *  _____ _____ __                   _____         _ _           
 * |     |     |  |      ___ ___ ___|     |___ ___|_| |_ ___ ___ 
 * |-   -| | | |  |__   | .'| . | . | | | | . |   | |  _| . |  _|
 * |_____|_|_|_|_____|  |__,|  _|  _|_|_|_|___|_|_|_|_| |___|_|  
 *                          |_| |_|                              
 *                    _                            _                       
 *            ___ ___|_|   ___ ___ ___ _ _ ___ ___| |_                     
 *           | .'| . | |  |  _| -_| . | | | -_|_ -|  _|                    
 *           |__,|  _|_|  |_| |___|_  |___|___|___|_|                      
 *               |_|                |_|                                                                     
 * ____________________________________________________________________________
 * 
 * APPMONITOR API CLIENT<br>
 * <br>
 * THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE <br>
 * LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR <br>
 * OTHER PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, <br>
 * EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED <br>
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE <br>
 * ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. <br>
 * SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY <br>
 * SERVICING, REPAIR OR CORRECTION.<br>
 * <br>
 * --------------------------------------------------------------------------------<br>
 * @author Axel Hahn
 * @link https://github.com/iml-it/appmonitor-api-client
 * @license GPL
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL 3.0
 * --------------------------------------------------------------------------------<br>
 * 2024-11-14  1.0  axel.hahn@unibe.ch  first lines
 * 2024-11-15  1.1  axel.hahn@unibe.ch  add showMessage; show total status of a group
 */

require '../php-class/appmonitorapi.class.php';

/**
 * Return codes of the IML appmonitor and their meaning
 * @var array
 */
$aReturncodes=[
    0 => 'OK',
    1 => 'Unknown',
    2 => 'Warning',
    3 => 'Error',
];

$sOut='';
$sMessages='';

$bShowErrorDetails=true;

// ----------------------------------------------------------------------
// FUNCTIONS
// ----------------------------------------------------------------------

/**
 * Show status of a single application
 * 
 * @param array$sApp
 * @return string
 */
function renderApp_lines(array $aAppdata): string
{
    global $aReturncodes;
    $sReturn='';

    $iResult=$aAppdata['result'];
    $sResult=$aReturncodes[$iResult] ?? '??';
    $sHost=$aAppdata['host'];
    $sAppname=$aAppdata['website'];

    return "<div class=\"app result-$iResult\">

        <span class=\"resultlabel\">$sResult</span>
        <strong>$sAppname</strong>
        ($sHost)

    </div>\n\n";
}

/**
 * Show a message
 * 
 * @param int $iLevel       Level - see $aReturncodes
 * @param string $sMessage  Message to show as html code
 * @return string
 */
function showMessage(int $iLevel, string $sMessage): string
{
    return "<div class=\"message result-$iLevel\">$sMessage</div>\n\n";
}

// ----------------------------------------------------------------------
// MAIN
// ----------------------------------------------------------------------

// ----- Init
$aConfig=include 'config.php';
$api = new appmonitorapi($aConfig['appmonitor']);


// ----- Loop over group emtries
foreach($aConfig['groups'] as $aGroup )
{
    $api->fetchByTags($aGroup['tags'], $aGroup['full']);

    // --- check errors
    if ( count($api->getErrors()) > 0 ) {
        $sMessages.="⚠️ Warning: Currently not all information is available from the monitoring system. The shown status is incomplete.<br><br>";
        if($bShowErrorDetails)
        {
            $sMessages.='<blockquote>';
            foreach ($api->getErrors() as $aError){
                $sMessages.=showMessage(3, "❌ $aError[url]<br>"
                    .($aError['response_header'] ? "<pre>$aError[response_header]</pre>" : "")
                    ."$aError[response_body]$aError[curlerrormsg]");
            }
            $sMessages.='</blockquote><br>';
        }
    }

    // --- generate output
    $iResulOfGroup=0;
    $sOutGroup='';
    foreach($api->getApps() as $sAppId)
    {
        $aAppdata=$api->getAppData($sAppId);
        $iResulOfGroup=max($iResulOfGroup, $aAppdata['result']);
        $sOutGroup.=''
            // for debugging remove next comment
            // .'<pre>'.print_r($api->getAppData($sAppId), 1).'</pre>'
            .renderApp_lines($aAppdata)
        ;
    };

    $sOut.="<h2><span class=\"result-$iResulOfGroup\">"
        .($aReturncodes[$iResulOfGroup] ?? '??')
        ."</span> $aGroup[label]</h2>"
        .$sOutGroup
    ;
}


// --- Legend
if($bShowErrorDetails)
{
    $sOut.='<br><h2>Visualization of all return codes</h2><br>';
    foreach($aReturncodes as $returncode => $sLabel)
    {
        $sOut.= renderApp_lines([
            'website' => "Test website",
            'host' => "srv-$returncode",
            'result' => $returncode,
        ]);
    }
}


// ----------------------------------------------------------------------
// OUTPUT HTML
// ----------------------------------------------------------------------

?><!doctype html>
<html>
    <meta http-equiv="refresh" content="30">
<head>
    <title>Health monitor</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <main>
    <?php echo $sMessages ?>
    <h1>Health monitor</h1>

    <?php echo date("H:i") . " <br><br>$sOut" ?>
    </main>
</body>
</html>