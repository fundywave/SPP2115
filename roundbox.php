<?php

/**
 * Check if the colour scheme has changed
 */
function rb_checkTheme() {//func

/******  get theme file names  ******/
  $theme = tpl_getConf('rb_theme');
  $file  = tpl_incdir().'style.ini';
  $file2 = tpl_incdir().'themes/'.$theme.'/style.ini';

/******  get current theme name  ******/
  $ini   = parse_ini_file($file);
  
/******  switch themes  ******/
  if ($ini['__theme__'] != $theme) {//do

  /******  switch themes  ******/
    if ((@file_exists($file2)) && (@unlink($file)) && (@copy($file2, $file))) {//do
      global $conf;
      if ($conf['fperm']) chmod($file, $conf['fperm']);
      echo '<meta http-equiv="Refresh" content=0 />';
      }//if ((@file_exists($file2)) && (@unlink($file)) && (@copy($file2, $file)))
      
  /******  theme not found  ******/
    else 
      msg('Could not set correct style.ini file for your chosen theme.', -1);

    }//if ($ini['__theme__'] != $theme) 
    
  }//function rb_checkTheme() 


/**
 * get user name and groups
 * Works around bug in DokuWiki <= 2009-09-22 and also appends
 * 'ALL' to list of groups to facilitate group access checking
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_get_user_info() {//func

/******  declare global variables  ******/
  global $INFO;
  
/******  set user array  ******/
  $user = array();
  
/******  no valid user  ******/
  if ($_REQUEST['do'] == 'logout' or !isset($INFO['userinfo'])) 
    $user['groups'] = array();

/******  set user information  ******/
  else {//do
    $user['groups'] = $INFO['userinfo']['grps'];
    $user['name'] = $_SERVER['REMOTE_USER'];
    }//else do

/******  add ALL  ******/
  array_push($user['groups'], 'ALL');

/******  return user  ******/
  return $user;

  }//function rb_get_user_info()
  

/**
 * check if current user should have access to given action
 * Relies on certain configuration parameters to be present
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_check_action_perms($action, $perms, $user) {//func

/******  declare global variables  ******/
  global $conf;

/******  set access control variables  ******/
  $ac_lvls = tpl_getConf('rb_act_ac_lvl');
  $ac_users = tpl_getConf('rb_act_users');
  $ac_lvl = $ac_lvls[$action];

/******  set default access level  ******/
  if (!isset($ac_lvl) or $ac_lvl == '') 
    $ac_lvl = 255;

/******  access level is ok  ******/
  if ($perms >= $ac_lvl) 
    return true;

/******  check access level  ******/
  elseif (!empty($ac_users[$action])) {//do
    $speclist = explode(',', $ac_users[$action]);
    $allowed_users = array();
    $allowed_groups = array();

    foreach ($speclist as $spec) {//do
      $spec = trim($spec);
      if (substr($spec, 0, 1) == '@') 
        array_push($allowed_groups, substr($spec, 1));
      else 
        array_push($allowed_users, $spec);
      }//foreach ($speclist as $spec) 

    if (!empty($info['user']) and
        in_array($user['name'], $allowed_users))
      return true;

    foreach ($user['groups'] as $group) {//do
      if (in_array($group, $allowed_groups)) 
        return true;
      }//foreach ($user['groups'] as $group) 

    }//if (!empty($ac_users[$action]))

/******  refuse access  ******/
  return false;

  }//function rb_check_action_perms($action, $perms, $user) 


/**
 * show button or link for given action depending on visibility settings
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_button($action, $perms, $user) {//func

/******  declare global variables  ******/
  global $conf;

/******  generate button/link if allowed  ******/
  if (rb_check_action_perms($action, $perms, $user)) {//do

  /******  generate link  ******/
    if (tpl_getConf('rb_uselinks')) {//do
      if (tpl_get_action($action)) {//do}    
        ptln('<li class="level1">');
        tpl_actionlink($action);
        ptln('</li>');
        }//if (tpl_get_action($action))     
      }//if (tpl_getConf('rb_uselinks'))

  /******  generate button  ******/
    else {//if not (tpl_getConf('rb_uselinks')) 
      tpl_button($action);
      ptln('<br>');
      }//if not (tpl_getConf('rb_uselinks')) 

    }//if (rb_check_action_perms($action, $perms, $user)) 

  }//function rb_button($action, $perms, $user)


/**
 * prints the contents of the command box in the sidebar
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_sitecmds($perms, $user) {//func

/******  declare global variables  ******/
  global $conf;

/******  start list of links  ******/
  if (tpl_getConf('rb_uselinks')) 
    ptln('<ul>');

/******  start button box  ******/
  else 
    ptln('<div class="buttonbox">');

/******  generate buttons/links  ******/
  foreach (tpl_getConf('rb_actions') as $command) {//do

  /******  generate break  ******/
    if ($command == '-') {//do
      if (tpl_getConf('rb_uselinks')) {//do
        ptln('</ul>');
        ptln('<ul>');
        }//if (tpl_getConf('rb_uselinks')) 
      else //if not (tpl_getConf('rb_uselinks')) 
        ptln('<br>');
      }//if ($command == '-') 

  /******  generate button/link  ******/
    else
      rb_button($command, $perms, $user);

    }//foreach (tpl_getConf('rb_actions') as $command)

/******  finish list of links  ******/
  if (tpl_getConf('rb_uselinks')) 
    ptln('</ul>');

/******  finish button box  ******/
  else 
    ptln('</div>');

  }//function rb_sitecmds($perms, $user) 
  

/**
 * replacement for tpl_youarehere()
 * Links namespaces to <namespace>:index
 *
 * taken from:
 * <http://wiki.splitbrain.org/wiki:tips:breadcrumb_namespace_index_links>
 */
function rb_youarehere() {//func

/******  declare global variables  ******/
  global $conf;
  global $ID;
  global $lang;

/******  break id into parts  ******/
  $parts = explode(':', $ID);

/******  show line header  ******/
  echo hsc($lang['youarehere']).': ';

/******  always show start page  ******/
  if ($a_part[0] != $conf['start']) {//do
    tpl_link(wl($conf['start']), $conf['start'],
        'title="'.$conf['start'].'"');
    }//if ($a_part[0] != $conf['start'])

/******  set variables  ******/
  $page = '';
  $last = count($parts);
  $count = 1;

/******  process each part  ******/
  foreach ($parts as $part) {//do

  /******  process part  ******/
    if ($count != $last || $part != tpl_getConf('rb_index')) {//do

    /******  skip start page if already done  ******/
      if ($part == $conf['start']) 
        continue;

    /******  show separator  ******/
      echo tpl_getConf('rb_crumbsep');

    /******  edit page  ******/
      $page .= $part;

    /******  remove underscores  ******/
      if (tpl_getConf('rb_removeunderscores') == 1) 
        $part = str_replace('_', ' ', $part);

    /******  set link variable  ******/
      if ($count == $last) 
        $link = $page;
      else 
        $link = "$page:" . tpl_getConf('rb_index');

    /******  show link if page exists  ******/
      if (file_exists(wikiFN($link))) 
        tpl_link(wl($link), $part, 'title="'.$link.'"');

    /******  show link, but mark as not-existing  ******/
      else 
        tpl_link(wl($link), $part, 'title="'.$link.'" class="wikilink2"');

    /******  add namespace separator  ******/
      $page .= ':';
      
      }//if ($count != $last || $part != tpl_getConf('rb_index')) 
      
  /******  increment count  ******/
    $count++;

    }//foreach ($parts as $part) 
    
  }//function rb_youarehere() 


/**
 * Show meta information for images
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_img_meta($debug=false) {//func

/******  declare global variables  ******/
  global $conf;
  global $lang;
  global $IMG;

/* change the order of fields in this array to change the order in which
 * the image information tags are listed
 */
  $tags = array(
    'img_caption' => array('IPTC.Caption', 'EXIF.UserComment',
        'EXIF.TIFFImageDescription', 'EXIF.TIFFUserComment'),
    'img_artist' => array('Iptc.Byline', 'Exif.TIFFArtist', 'Exif.Artist',
        'Iptc.Credit'),
    'img_keywords' => array('IPTC.Keywords','IPTC.Category'),
    'img_copyr' => array('Iptc.CopyrightNotice', 'Exif.TIFFCopyright',
        'Exif.Copyright'),
    'img_camera' => 'Simple.Camera',
    'img_fname' => 'File.Name',
    'img_format' => 'File.Format',
    'img_dimen'=> null, // built from File.Width and File.Height
    'img_date' => 'Date.EarliestTime',
    'img_fsize'=> 'File.NiceSize',
    );

/******  start table  ******/
  ptln('<table class="img_tags">');

/******  show caption  ******/
  ptln('<caption>', 2);
  ptln($lang['img_metaheading'], 4);
//  if (tpl_img_getTag('File.Mime') == 'image/jpeg') 
    rb_btn_img_meta_edit($IMG, true);
  ptln('</caption>', 2);

/******  show column headers  ******/
  ptln('<thead>', 2);
  ptln('<tr>', 4);
  ptln('<th class="label">' . $lang['colfield'] . '</th>', 6);
  ptln('<th class="value">' . $lang['colvalue'] . '</th>', 6);
  ptln('</tr>', 4);
  ptln('</thead>', 2);

/******  generate table  ******/
  ptln('<tbody>', 2);

  foreach ($tags as $tagname => $meta) {//do

    if ($tagname == 'img_dimen') {//do
      $info = tpl_img_getTag('File.Width') . '&#215;' .
          tpl_img_getTag('File.Height');
      }//if ($tagname == 'img_dimen') 

    else {//if not ($tagname == 'img_dimen') 
      $info = tpl_img_getTag($meta);
      if ($info && $tagname == 'img_date') 
        $info = dformat($info, $conf['dformat']);
      else 
        $info = nl2br(hsc($info));
      }//if not ($tagname == 'img_dimen') 

    if ($info) {//do
      ptln('<tr>', 4);
      ptln('<td class="label">' . hsc($lang[$tagname]) . '</td>', 6);
      ptln('<td class="value">' . $info . '</td>', 6);
      ptln('</tr>', 4);
      }//if ($info) 
    }//foreach ($tags as $tagname => $meta) 

  ptln('</tbody>', 2);
  ptln('</table>');

/******  debug  ******/
  if ($debug) 
    dbg(tpl_img_getTag('Simple.Raw'));

  }//function rb_img_meta($debug=false) 

/**
 * Show link to mediaedit page
 *
 * @author Chris Arndt <chris@chrisarndt.de>
 */
function rb_btn_img_meta_edit($id, $newwin=false) {//func

/******  declare global variables  ******/
  global $AUTH;
  global $lang;

/******  show link  ******/
  if ($AUTH >= AUTH_UPLOAD) {//do
    echo '<a href="' . DOKU_BASE . 'lib/exe/mediamanager.php?edit=' .
        urlencode($id) . '"';
    if ($newwin) 
      echo ' onclick="return metaedit(\'' . urlencode($id) .
          '\');" target="mediaselect"';
    echo '>';
    echo '<img src="' . DOKU_BASE . 'lib/images/edit.gif" alt="' .
        $lang['metaedit'] . '" title="' . $lang['metaedit'] . '" />';
    ptln('</a>');
    }//if ($AUTH >= AUTH_UPLOAD) 

  }//function rb_btn_img_meta_edit($id, $newwin=false) 

?>
