<?php
/*
 * DokuWiki Roundbox Template
 *
 * A template with a sidebar after the style of my homepage
 * <http://chrisarndt.de/>
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:roundbox
 * @author Chris Arndt <chris@chrisarndt.de>
 * @author Don Bowman  <don@lynsoft.co.uk>
 */

/******  include discussion code  ******/
  if (in_array('discussion', plugin_list('syntax'))) {//do
    include(DOKU_PLUGIN.'discussion/discussion.php');
    $have_discussion = true;
    }//if (in_array('discussion', plugin_list('syntax'))) 
  else 
    $have_discussion = false;

/******  include template configuration and translations  ******/
//  include_once(dirname(__FILE__).'/conf/default.php');
  include_once(dirname(__FILE__).'/lang/en/lang.php');
  @include_once(dirname(__FILE__).'/lang/'.$conf['lang'].'/lang.php');

/******  include sidebar code  ******/
  if (tpl_getConf('rb_sitenav')) 
    include(dirname(__FILE__).'/sidebar.php');

/******  include template helper functions  ******/
  include_once(dirname(__FILE__).'/roundbox.php');


  $user = rb_get_user_info();
  $perms = auth_quickaclcheck($ID);

/*
 * we must move the doctype down (unfortunately) - headers need to be first
 */
?>
<?php
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
*/
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
        <?php tpl_pagetitle()?>
        [<?php echo hsc($conf['title'])?>]
    </title>

    <?php tpl_metaheaders()?>

    <?php rb_checkTheme()?>

    <link rel="shortcut icon" href="<?php echo tpl_basedir()?>images/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <?php if (tpl_getConf('rb_roundcorners')) { ?>
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo tpl_basedir()?>roundcorners.css" />
    <?php } ?>

    <!--[if gte IE 5]>
  <style type="text/css">
    /* that IE 5+ conditional comment makes this only visible in IE 5+ */
    /* IE bugfix for transparent PNGs */
    //DISABLED   img { behavior: url("<?php echo DOKU_BASE?>lib/scripts/pngbehavior.htc"); }
  </style>
  <![endif]-->



    <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>

    <?php
    /*Loading menu style*/
    $style = tpl_basedir() . 'customstyles/general.css?'.date('Ymdhis');
    $style1 = tpl_basedir() . 'customstyles/bootstrapSubmenu.css?'.date('Ymdhis');
    echo "<link rel='stylesheet' type='text/css' href=$style>";
    echo "<link rel='stylesheet' type='text/css' href=$style1>";
    ?>

</head>

<body>
    <?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<!-- carousel is only visible only in home page-->
    <?php
      $link = $_SERVER['REQUEST_URI'];
    
          if(strpos($link, "id")>0)
          {
            $pos = strpos($link, "=");
                                    
            $page = substr($link, $pos + 1, strlen($link) - $pos);
            if ($page == "home") 
            {
              @include(dirname(__FILE__).'/carousel.html');
            }
          }
          else {
            $pos = strpos($link, "doku.php");
                                    
            $page = substr($link, $pos, strlen($link) - $pos);
            if ($page == "doku.php") 
            {
              @include(dirname(__FILE__).'/carousel.html');
            }
          }
    ?>

    <!-- start dokuwiki block -->
    <div class="dokuwiki">

        <!-- start header block -->
        <div id="header">

            <!-- start header title -->
            <div id="header_title">
                <div class="logo">
                    <?php tpl_link(wl(),$conf['title'],'name="dokuwiki__top" id="dokuwiki__top" accesskey="h" title="[ALT+H]"')?>
                </div>
                <div class="pagename">
                    [[&nbsp;<?php if (rb_check_action_perms('backlink', $perms, $user)) {
          tpl_link(wl($ID,'do=backlink'),$ID);
        } else {
        echo hsc($ID); } ?>&nbsp;]]
                </div>
            </div>
            <!-- end header title -->

            <!-- start lower header part -->
            <div class="bar" id="header_bar">
                <!-- start tagline -->
                <div class="bar__left" id="bar__topleft">
                    <span class="tagline"><?php echo hsc(tpl_getConf('rb_tagline')) ?></span>
                </div>
                <!-- end tagline -->

                <!-- start goto & search area -->
                <div class="bar__right" id="bar__topright">
                    <?php if (rb_check_action_perms('goto', $perms, $user)) { ?>
                    <form action="<?php echo DOKU_BASE ?>doku.php" accept-charset="utf-8" class="search" name="goto">
                        <input type="text" accesskey="g" name="id" class="edit"
                            title="<?php echo $lang['tip_goto'] ?> [ALT-G]" />
                        <input type="submit" value="<?php echo $lang['btn_goto']?>" class="button"
                            title="<?php echo $lang['tip_goto'] ?> [ALT-G]" />
                    </form>
                    <?php } ?>
                    <?php if (rb_check_action_perms('search', $perms, $user)) { ?>
                    <?php tpl_searchform()?>&nbsp;
                    <?php } ?>
                </div>
                <!-- end goto & search area -->

            </div>
            <!-- end lower header part -->

        </div>
        <!-- end header block -->
        <?php /*old includehook*/ @include(dirname(__FILE__) . '/pageheader.html') ?>
        <?php /*old includehook*/ @include(dirname(__FILE__).'/header.html')?>
        <?php flush()?>

        <!-- start content block -->
        <div id="container">

            <!-- start wikipage block -->
            <div class="centerpage">

                <?php html_msgarea()?>

                <!-- wikipage start -->
                <?php 
    if ($ACT == "register" || $ACT == "resendpwd") 
      tpl_content();
    else if (tpl_getConf('rb_private') && !$_SERVER['REMOTE_USER']) 
      html_login();
    else 
      tpl_content();
  ?>
                <?php flush()?>

            </div>
            <!-- end dokuwiki block -->

            <?php tpl_indexerWebBug() ?>

</body>

</html>