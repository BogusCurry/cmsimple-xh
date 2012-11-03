<?php

/**
 * @version $Id$
 */

/* utf8-marker = äöü */
/*
  ======================================
  $CMSIMPLE_XH_VERSION$
  $CMSIMPLE_XH_DATE$
  based on CMSimple version 3.3 - December 31. 2009
  For changelog, downloads and information please see http://www.cmsimple-xh.com
  ======================================
  -- COPYRIGHT INFORMATION START --

  CMSimple version 3.3 - December 31. 2009
  Small - simple - smart
  © 1999-2009 Peter Andreas Harteg - peter@harteg.dk

  -- COPYRIGHT INFORMATION END --

  -- LICENCE TYPES SECTION START --

  CMSimple is available under four different licenses:

  1) GPL 3
  From December 31. 2009 CMSimple is released under the GPL 3 licence with no link requirments. You may not remove copyright information from the files, and any modifications will fall under the copyleft conditions in the GPL 3.

  2) AGPL 3
  You must keep a convenient and prominently visible feature on every generated page that displays the CMSimple Legal Notices. The required link to the CMSimple Legal Notices must be static, visible and readable, and the text in the CMSimple Legal Notices may not be altered.
  See http://www.cmsimple.org/?Licence:CMSimple_Legal_Notices

  3) Linkware / CMSimple Link Requirement Licence
  Same as AGPL, but instead of keeping a link to the CMSimple Legal Notices, you must place a static, visible and readable link to www.cmsimple.org with the text or an image stating "Powered by CMSimple" on every generated page (place it in the template).
  See http://www.cmsimple.org/?Licence:CMSimple_Link_Requirement_Licence

  4) Commercial Licence
  This licence will allow you to remove the CMSimple Legal Notices / "Powered by CMSimple"-link at one specific domain. This licence will also protect your modifications against the copyleft requirements in AGPL 3 and give access to registering in user support forum.

  You may change this LICENCE TYPES SECTION to relevant information, if you have purchased a commercial licence, but then the files may not be distributed to any other domain not covered by a commercial licence.

  For further informaion about the licence types, please see http://www.cmsimple.org/?Licence and /cmsimple/legal.txt

  -- LICENCE TYPES SECTION END -
  ======================================
 */

$title = '';
$o = '';
$e = '';
$hjs = '';
$bjs = '';
$onload = '';

// added to make it possible to overwrite the backend plugins delivered with the core

//$backend_hooks = array(
//  'pagemanager' => false,
//  'filebrowser' => false
//);

//HI 2009-10-30 (CMSimple_XH 1.0rc3) added version-informations
define('CMSIMPLE_XH_VERSION', '$CMSIMPLE_XH_VERSION$');
define('CMSIMPLE_XH_BUILD', '$CMSIMPLE_XH_BUILD$');
define('CMSIMPLE_XH_DATE', '$CMSIMPLE_XH_DATE$');
//version-informations

if (preg_match('/cms.php/i', $_SERVER['PHP_SELF']))
    die('Access Denied');
    
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 8192);
}
if (!defined('E_USER_DEPRECATED')) {
    define('E_USER_DEPRECATED', 16384);
}

$pth['file']['execute'] = './index.php';
$pth['folder']['content'] = './content/';
$pth['file']['content'] = $pth['folder']['content'] . 'content.htm';
$pth['file']['pagedata'] = $pth['folder']['content'] . 'pagedata.php';

$pth['folder']['base'] = is_dir('./cmsimple') ? './' : '../'; 
 
$pth['folder']['cmsimple'] = $pth['folder']['base'] . 'cmsimple/';
$pth['folder']['classes'] = $pth['folder']['cmsimple'] . 'classes/';

$pth['file']['log'] = $pth['folder']['cmsimple'] . 'log.txt';
$pth['file']['cms'] = $pth['folder']['cmsimple'] . 'cms.php';
$pth['file']['config'] = $pth['folder']['cmsimple'] . 'config.php';

if (file_exists($pth['folder']['cmsimple'].'defaultconfig.php')) {
    include($pth['folder']['cmsimple'].'defaultconfig.php');
}
if (!include($pth['file']['config']))
    die('Config file missing');
    
if (!empty($cf['functions']['file'])) {
    include_once $pth['folder']['cmsimple'] . $cf['functions']['file'];
}

foreach (array('userfiles', 'downloads', 'images', 'media') as $temp) {
    if (!isset($cf['folders'][$temp])) { // for compatibility with older version's config files
	$cf['folders'][$temp] = $temp != 'media' ? "$temp/" : 'downloads/';
    }
    $pth['folder'][$temp] = $pth['folder']['base'] . $cf['folders'][$temp];
}

$pth['folder']['flags'] = $pth['folder']['images'] . 'flags/';

//HI 2009-10-30 (CMSimple_XH 1.0rc3) debug-mode, enables error-reporting
xh_debugmode();
$errors = array();

$pth['folder']['language'] = $pth['folder']['cmsimple'] . 'languages/';
$pth['folder']['langconfig'] = $pth['folder']['cmsimple'] . 'languages/';
if (preg_match('/\/[A-z]{2}\/[^\/]*/', sv('PHP_SELF')))
    $sl = strtolower(preg_replace('/.*\/([A-z]{2})\/[^\/]*/', '\1', sv('PHP_SELF')));

$temp = explode('/', str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
$temp = $temp[count($temp) - 2];
if (is_file('./cmsimplesubsite.htm')) {
    $sl = $temp;
}

if (!isset($sl))
    $sl = $cf['language']['default'];
$pth['file']['language'] = $pth['folder']['language'] . basename($sl) . '.php';
$pth['file']['langconfig'] = $pth['folder']['language'] . basename($sl) . 'config.php';
$pth['file']['corestyle'] = $pth['folder']['base'] . 'css/core.css';

XH_createLanguageFile($pth['file']['language']);
if (!file_exists($pth['file']['language']) && !file_exists($pth['folder']['language'].'default.php')) {
    die('Language file ' . $pth['file']['language'] . ' missing');
}

XH_createLanguageFile($pth['file']['langconfig']);
if (!file_exists($pth['file']['langconfig']) && !file_exists($pth['folder']['language'].'defaultconfig.php')) {
    die('Language config file ' . $pth['file']['langconfig'] . ' missing');
}

include $pth['folder']['language'] . 'default.php';
include $pth['file']['language'];
include $pth['folder']['language'] . 'defaultconfig.php';
include $pth['file']['langconfig'];

$pth['folder']['templates'] = $pth['folder']['base'] . 'templates/';
$pth['folder']['template'] = $pth['folder']['templates'] . $cf['site']['template'] . '/';

$temp = $txc['subsite']['template'] == ''
    ? $cf['site']['template']
    : $txc['subsite']['template'];
$pth['folder']['template'] = $pth['folder']['templates'] . $temp . '/';
$pth['file']['template'] = $pth['folder']['template'] . 'template.htm';
$pth['file']['stylesheet'] = $pth['folder']['template'] . 'stylesheet.css';
$pth['folder']['menubuttons'] = $pth['folder']['template'] . 'menu/';
$pth['folder']['templateimages'] = $pth['folder']['template'] . 'images/';

$pth['folder']['plugins'] = $pth['folder']['base'] . 'plugins/';
$pth['file']['pluginloader'] = $pth['folder']['cmsimple'] . 'pluginloader.php';

require_once $pth['folder']['plugins'] . 'utf8/utf8.php';
require_once UTF8 . '/ucfirst.php';
require_once UTF8 . '/utils/validation.php';

// don't check cookies, as these might be set from non UTF-8 scripts on the domain
// TODO: what about the variable names? what about other input (e.g. $_SERVER)?
XH_checkValidUtf8(array($_GET, $_POST));

$iis = strpos(sv('SERVER_SOFTWARE'), "IIS");
$cgi = (php_sapi_name() == 'cgi' || php_sapi_name() == 'cgi-fcgi');

$sn = preg_replace('/([^\?]*)\?.*/', '\1', sv(($iis ? 'SCRIPT_NAME' : 'REQUEST_URI')));
foreach (array('download', 'function', 'media', 'search', 'mailform', 'sitemap', 'text', 'selected', 'login', 'logout', 'settings', 'print', 'file', 'action', 'validate', 'images', 'downloads', 'edit', 'normal', 'stylesheet', 'passwd', 'userfiles', 'xhpages')as $i)
    initvar($i);

//by GE 2009-10-14 (CMSimple_XH 1.0rc2)
define('CMSIMPLE_ROOT', str_replace('index.php', '', str_replace('/' . $sl . '/', "/", $sn))); //for absolute references
define('CMSIMPLE_BASE', (strtolower($cf['language']['default']) == $sl ? './' : './../')); //for relative references
//END by GE 2009-10-14 (CMSimple_XH 1.0rc2)
// define su - selected url
$su = '';
if (sv('QUERY_STRING') != '') {
    $rq = explode('&', sv('QUERY_STRING'));
    if (!strpos($rq[0], '='))
        $su = $rq[0];
    $v = count($rq);
    for ($i = 0; $i < $v; $i++)
        if (!strpos($rq[$i], '='))
            $GLOBALS[$rq[$i]] = 'true';
}
else
    $su = $selected;
if (!isset($cf['uri']['length']))
    $cf['uri']['length'] = 200;
$su = substr($su, 0, $cf['uri']['length']);

if ($stylesheet != '') {
    header("Content-type: text/css");
    include($pth['file']['stylesheet']);
    exit;
}
if ($download != '')
    download($pth['folder']['downloads'] . basename($download));

$pth['file']['login'] = $pth['folder']['cmsimple'] . 'login.php';

$pth['file']['search'] = $pth['folder']['cmsimple'] . 'search.php';
$pth['file']['mailform'] = $pth['folder']['cmsimple'] . 'mailform.php';

$adm = 0;
$f = '';
if (!@include($pth['file']['login']))
    if ($login)
        e('missing', 'file', $pth['file']['login']);

$cl = 0;
rfc(); // Here content is loaded

if ($function == 'search')
    $f = 'search';
if ($mailform || $function == 'mailform')
    $f = 'mailform';
if ($sitemap)
    $f = 'sitemap';
if ($xhpages)
    $f = 'xhpages';

    
// includes additional userfuncs.php - CMSimple_XH beta3
if (is_readable($pth['folder']['cmsimple'] . 'userfuncs.php')) {
    include_once $pth['folder']['cmsimple'] . 'userfuncs.php';
}



// changes title, keywords and description from $tx to $cf - by MD 2009/08 (CMSimple_XH beta)

foreach ($txc['meta'] as $key => $param) {
    if (strlen(trim($param)) > 0 && $key != 'codepage') {
        $cf['meta'][$key] = $param;
    }
}
foreach ($txc['site'] as $key => $param) {
    if (strlen(trim($param)) > 0) {
        $cf['site'][$key] = $param;
    }
}
foreach ($txc['mailform'] as $key => $param) {
    if (strlen(trim($param)) > 0) {
        $cf['mailform'][$key] = $param;
    }
}
// END of code added for (CMSimple_XH beta)

if (strcasecmp($tx['meta']['codepage'], 'UTF-8') != 0) {
    $e .= '<li>' . sprintf('<b>UTF-8 encoding required, but codepage %s found!</b>', $tx['meta']['codepage']) . tag('br')
	. 'Please change that in Settings&rarr;Language&rarr;Meta&rarr;Codepage'
	. ' and convert all files to UTF-8 without BOM, if not already done.</li>' . "\n";
}

// Plugin loading
if ($function == 'save') {
    $edit = true;
}

$adm and include_once $pth['folder']['cmsimple'] . 'adminfuncs.php';
require_once $pth['folder']['cmsimple'] . 'tplfuncs.php';

include($pth['file']['pluginloader']);

if ($f == 'search')
    @include($pth['file']['search']);
if ($f == 'mailform' && $cf['mailform']['email'] != '')
    include($pth['file']['mailform']);
if ($f == 'sitemap') {
    $title = $tx['title'][$f];
    $ta = array();
    $o .= '<h1>' . $title . '</h1>' . "\n";
    for ($i = 0; $i < $cl; $i++)
        if (!hide($i) || $cf['show_hidden']['pages_sitemap'] == 'true')
            $ta[] = $i;
    $o .= li($ta, 'sitemaplevel');
}

// Compatibility for DHTML menus, moved from functions.php to cms.php - by MD 2009/09 (CMSimple_XH 1.0rc1)
$si = -1;
$hc = array();
for ($i = 0; $i < $cl; $i++) {
    if (!hide($i) || ($i == $s && $cf['show_hidden']['pages_toc'] == 'true'))
        $hc[] = $i;
    if ($i == $s)
        $si = count($hc);
}
$hl = count($hc);
//END Compatibility for DHTML menus, moved from functions.php to cms.php - by MD 2009/09 (CMSimple_XH 1.0rc1)
// LEGAL NOTICES - no needed under GPL3
if (@$cf['menu']['legal'] == '')
    $cf['menu']['legal'] = 'CMSimple Legal Notices';
if ($su == uenc($cf['menu']['legal'])) {
    $f = $title = $cf['menu']['legal'];
    $s = -1;
    $o .= '<h1>' . $title . '</h1>' . rf($pth['folder']['cmsimple'] . 'legal.txt');
}

if ($adm) {
    
    if ($validate) {
        $f = 'validate';
    }
    if ($settings) {
        $f = 'settings';
    }
    if (isset($sysinfo)) { // FIXME: why isset() here and not in the other ifs?
        $f = 'sysinfo';
    }
    if (isset($phpinfo)) { // FIXME: why isset() here and not in the other ifs?
        $f = 'phpinfo';
    }
    if ($file) {
        $f = 'file';
    }
    if ($userfiles) {
        $f = 'userfiles';
    }
    if ($images || $function == 'images') {
        $f = 'images';
    }
    if ($downloads || $function == 'downloads') {
        $f = 'downloads';
    }
    if ($function == 'save') {
        $f = 'save';
    }

    if ($f == 'settings' || $f == 'images' || $f == 'downloads'
        || $f == 'validate' || $f == 'sysinfo' || $f == 'phpinfo')
    {
        $title = $tx['title'][$f];
        $o .= "\n\n" . '<h1>' . $title . '</h1>' . "\n";
    }

    switch ($f) {
    case 'sysinfo':
        $o .= XH_sysinfo();
        break;
    case 'phpinfo':
        phpinfo();
        exit;
    case 'settings':
        $o .= XH_settingsView();
        break;
    case 'file':
        if (preg_match('/^\d{8}_\d{6}_(?:content.htm|pagedata.php)$/', $file)) {
            $pth['file'][$file] = $pth['folder']['content'] . '/' . $file;
        }
        if ($pth['file'][$file] != '') {
            if ($action == 'view') {
                header('Content-Type: text/plain; charset=utf-8');
                echo rmnl(rf($pth['file'][$file]));
                exit;
            }
            if ($action == 'download') {
                download($pth['file'][$file]);
            } else {
                require_once $pth['folder']['classes'] . 'FileEdit.php';
                $temp = array('config' => 'XH_CoreConfigFileEdit',
                              'langconfig' => 'XH_CoreLangconfigFileEdit',
                              'language' => 'XH_CoreLangFileEdit',
                              'template' => 'XH_CoreTextFileEdit',
                              'stylesheet' => 'XH_CoreTextFileEdit');
                $temp = array_key_exists($file) ? $temp[$file] : null;
                if ($action == 'save') {
                    $o .= $temp->submit();
                } else {
                    $o .= $temp->form();
                }
            }
        }
        break;
    case 'validate':
        require_once $pth['folder']['classes'] . 'LinkCheck.php';
        $temp = new XH_LinkCheck();
        $o .= $temp->check_links();
        break;
    }
}


if ($s == -1 && !$f && $o == '' && $su == '') {
    $s = 0;
    $hs = 0;
}

// SAVE

if ($adm && $f == 'save') {
    $ss = $s;
    $c[$s] = $text;

    if ($s == 0) {
        if (!preg_match("/^<h1[^>]*>.*<\/h1>/i", rmanl($c[0]))
            && !preg_match("/^(<p[^>]*>)?(\&nbsp;| |<br \/>)?(<\/p>)?$/i", rmanl($c[0])))
        {
            $c[0] = '<h1>' . $tx['toc']['missing'] . '</h1>' . "\n" . $c[0];
        }
    }
    $title = utf8_ucfirst($tx['filetype']['content']);

    if ($fh = @fopen($pth['file']['content'], "w")) {
        fwrite($fh, '<html><head><title>Content</title></head><body>' . "\n");
        foreach ($c as $i) {
            fwrite($fh, rmnl($i . "\n"));
        }
        fwrite($fh, '</body></html>');
        fclose($fh);

        preg_match('~<h[1-'.$cf['menu']['levels'].'][^>]*>(.+?)</h[1-'.$cf['menu']['levels'].']>~isu', $c[$s], $matches);
        if (count($matches) > 0) {
            $temp = explode($cf['uri']['seperator'], $selected);
            array_splice($temp, -1, 1, uenc(trim(xh_rmws(strip_tags($matches[1])))));
            $su = implode($cf['uri']['seperator'], $temp);
        } else {
            $su = $u[max($s - 1, 0)];
        }
        header("Location: " . $sn . "?" . $su);
        exit;
    } else {
        e('cntwriteto', 'content', $pth['file']['content']);
    }
    $title = '';
}

if ($adm && $edit && (!$f || $f == 'save') && !$download) {
    if (isset($ss)) {
        if ($s < 0 && $ss < $cl) {
            $s = $ss;
        }
    }
    if ($s > -1) {
        $su = $u[$s];

        $editor = $cf['editor']['external'] == '' || init_editor();
        if (!$editor) {
            $e .= '<li>'.sprintf('External editor %s missing', $cf['editor']['external']).'</li>'."\n";
        }
        $o .= '<form method="post" id="ta" action="' . $sn . '">'
                . tag('input type="hidden" name="selected" value="' . $u[$s] . '"')
                . tag('input type="hidden" name="function" value="save"')
                . '<textarea name="text" id="text" class="xh-editor" style="height: '
                . $cf['editor']['height'] . 'px; width: 100%;" rows="30" cols="80">'
                . htmlspecialchars($c[$s], ENT_COMPAT, 'UTF-8')
                . '</textarea>';
        if ($cf['editor']['external'] == '' || !$editor) {
            $o .= tag('input type="submit" value="' . utf8_ucfirst($tx['action']['save']) . '"');
        }
        $o .= '</form>';
    } else {
        $o .= '<p>' . $tx['error']['cntlocateheading'] . '</p>' . "\n";
    }
}

if ($adm && ((isset($images) && $images)
             || (isset($downloads) && $downloads)
             || (isset($userfiles) && $userfiles)
             || (isset($media) && $media)
             || $edit && (!$f || $f == 'save') && !$download))
{
    if ($cf['filebrowser']['external'] && !file_exists($pth['folder']['plugins'] . $cf['filebrowser']['external'])) {
        $e .= '<li>' . sprintf('External filebrowser %s missing', $cf['filebrowser']['external']) . '</li>' . "\n";
    }
}

if ($adm && $f == 'xhpages') {
    if ($cf['pagemanager']['external'] && !file_exists($pth['folder']['plugins'] . $cf['pagemanager']['external'])) {
        $e .= '<li>' . sprintf('External pagemanager %s missing', $cf['pagemanager']['external']) . '</li>' . "\n";
    }
}



// CMSimple scripting
if (!($edit && $adm) && $s > -1) {
    $c[$s] = evaluate_cmsimple_scripting($c[$s]);
    if (isset($keywords))
	$cf['meta']['keywords'] = $keywords;
    if (isset($description))
	$cf['meta']['description'] = $description;
}


if ($s == -1 && !$f && $o == '')
    shead('404');

if (function_exists('loginforms'))
    loginforms();

foreach (array('content', 'pagedata', 'config', 'language', 'langconfig', 'stylesheet', 'template', 'log') as $i)
    chkfile($i, (($login || $settings) && $adm));
if ($e)
    $o = '<div class="cmsimplecore_warning cmsimplecore_center">' . "\n" . '<b>' . $tx['heading']['warning'] . '</b>' . "\n" . '</div>' . "\n" . '<ul>' . "\n" . $e . '</ul>' . "\n" . $o;
if ($title == '') {
    if ($s > -1)
        $title = $h[$s];
    else if ($f != '')
        $title = ucfirst($f);
}

if (!headers_sent($tempFile, $tempLine)) {
    header('Content-Type: text/html; charset=' . $tx['meta']['codepage']);
} else {
    $temp = $tempFile . ':' . $tempLine;
    exit(str_replace('{location}', $temp, $tx['error']['headers']));
}

if ($print) {
    if ($cf['xhtml']['endtags'] == 'true') {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
        ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n" .
        '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
    } else {
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"',
        ' "http://www.w3.org/TR/html4/loose.dtd">' . "\n" . '<html>' . "\n";
    }
    echo '<head>' . "\n" . head(),
    '<meta name="robots" content="noindex">' . "\n" .
    '</head>' . "\n" . '<body class="print"', onload(), '>' . "\n" .
    content(), '</body>' . "\n" . '</html>' . "\n";
    exit;
}


if (!XH_ADM && $adm) {
    $s = -1;
    $adm = $edit = false;
    $o = '';
    $f = 'login';
    loginforms();
}


ob_start('final_clean_up');

if (!include($pth['file']['template'])) {
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain; charset=utf-8');
    echo $tx['error']['missing'], ' ', $tx['filetype']['template'], "\n", $pth['file']['template'];
    exit;
}

?>
