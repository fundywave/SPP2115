<?php 
/*
 * Provide navigation sidebar functionality to Dokuwiki Templates
 *
 * This is not currently part of the official Dokuwiki release
 *
 * @author Christopher Smith <chris@jalakai.co.uk>
 * @author Don Bowman <don@lynsoft.co.uk>
 */

/******  sidebar configuration settings  ******/
  tpl_loadConfig();

/******  determine the sidebar class  ******/
  $sidebar_class = 
      "sidebar sidebar_".tpl_getConf('layout').'_'.tpl_getConf('orientation');


/*
 * recursive function to establish best sidebar file to be used
 */
function getSidebarFN($ns, $file) {//func

/******  check for wiki page = $ns:$file (or $file where no namespace)  ******/
  $nsFile = ($ns) ? "$ns:$file" : $file;
  if (file_exists(wikiFN($nsFile)) && auth_quickaclcheck($nsFile)) 
    return $nsFile;
  
/******  no namespace left, exit with no file found  ******/
  if (!$ns) 
    return '';
  
/******  remove deepest namespace level and call function recursively  ******/
  $i = strrpos($ns, ":");
  $ns = ($i) ? substr($ns, 0, $i) : false;  
  return getSidebarFN($ns, $file);

  }//function getSidebarFN($ns, $file) 


/*
 * print a sidebar edit button - if appropriate
 */
function tpl_sidebar_editbtn() {//func

/******  declare global variables  ******/
  global $ID, $conf, $lang;

/******  check if button wanted  ******/
  if (!tpl_getConf('rb_showeditbtn')) 
    return;
  
/******  check if sidebar page exists  ******/
  $fileSidebar = getSidebarFN(getNS($ID), 'sidebar');
  if (!$fileSidebar) 
    return;
  
/******  check if user has edit permission for the sidebar page  ******/
  if (auth_quickaclcheck($fileSidebar) < AUTH_EDIT) 
    return;
  
/******  generate button  ******/
  ?>
    <div class="secedit">
      <form class="button" method="post" action="<?php echo wl($fileSidebar,'do=edit'); ?>" 
          onsubmit="return svchk()">
        <input type="hidden" name="do" value="edit" />
        <input type="hidden" name="rev" value="" />
        <input type="hidden" name="id" value="<?php echo $fileSidebar; ?>" />
        <input type="submit" value="<?php echo $lang['btn_sidebaredit']; ?>" class="button" />
      </form>
    </div>
  <?php

  }//function tpl_sidebar_editbtn() 


/*
 * display the sidebar
 */
function tpl_sidebar_content() {//func

/******  declare global variables  ******/
  global $ID, $REV, $ACT, $conf;
  
/******  save global variables  ******/
  $saveID = $ID;
  $saveREV = $REV;
  $saveACT = $ACT;

/******  find file to be displayed in navigation sidebar  ******/
  $fileSidebar = '';
  $fileSidebar = getSidebarFN(getNS($ID), 'sidebar');

/******  show main sidebar if necessary  ******/
  if (tpl_getConf('rb_main_sidebar')  && 
      $fileSidebar != 'sidebar'       &&
      file_exists(wikiFN('sidebar'))) {//do
    $ID = 'sidebar';
    $REV = '';
    $ACT = 'show';
    tpl_content(false);
    if (tpl_getConf('rb_showeditbtn')) {//do
      tpl_sidebar_editbtn();
      echo "<br>";
      }//if (tpl_getConf('rb_showeditbtn'))
    echo "<hr>";
    }//if (tpl_getConf('rb_main_sidebar')  && ... 

/******  show current sidebar  ******/
  if ($fileSidebar) {//do
    $ID = $fileSidebar;
    $REV = '';
    $ACT = 'show';
    tpl_content(false);
    if (tpl_getConf('rb_showeditbtn')) 
      tpl_sidebar_editbtn();
    }//if ($fileSidebar)

/******  show index  ******/
  else {//if (!$fileSidebar)
    $REV = '';
    $ACT = 'index';
    tpl_content(false);
    }//if (!$fileSidebar)
    
/******  restore global variables  ******/
  $ID = $saveID;
  $REV = $saveREV;
  $ACT = $saveACT;

  }//function tpl_sidebar_content() 
