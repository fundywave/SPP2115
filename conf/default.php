<?php
/*
 * additional configuration options used by the template
 * See http://www.splitbrain.org/wiki:tpl:roundbox#configuration for more info
 */
$conf['rb_theme'] = 'evening'; // theme

$conf['rb_tagline'] = ''; // tagline under wiki title

$conf['rb_roundcorners'] = true; // main boxes with round corners? (mozilla only)

$conf['rb_sidebar_orientation'] = 'left'; // side the sidebar is on left|right
$conf['rb_sitenav'] = true; // show site navigation in sidebar true|false
$conf['rb_uselinks'] = true; // use links instead of buttons
$conf['rb_main_sidebar']      = false; // Always show main sidebar
$conf['rb_showeditbtn']       = false; // Show Edit button on sidebar

$conf['rb_youarehere']        = true; // hierarchical navigation instead of breadcrumbs
$conf['rb_crumbsep']          = ' &rarr; '; // Specifies what separates each breadcrumb
$conf['rb_removeunderscore']  = true;  // Removes underscore from breadcrumb links
$conf['rb_index'] = 'start'; // Sets the name for the index page of namespaces

$conf['rb_private']           = false; // Private wiki

// which actions should be available in the command box in the sidebar?
$conf['rb_actions'] = array(
    'edit', 
    'revert', 
    'history', 
    'subscribe', 
    'backlink', 
    '-', 
    'index', 
    'recent', 
    '-', 
    'admin', 
    '-', 
    'profile', 
    'login');

$conf['rb_act_ac_lvl'] = array(
    // only admins can see the 'Admin' button
    'admin' => AUTH_ADMIN,
    // Show "Edit this page" only to users that have at least edit level access
    'edit' => AUTH_EDIT,
    // same for "Old revisions"
    'revert' => AUTH_EDIT,
    // same for "Old revisions"
    'history' => AUTH_EDIT,
    // and "Recent changes"
    'recent' => AUTH_EDIT,
    // Show 'Backlinks' to all
    'backlink' => '',
    //'index' => '',
    //'login' => '',
    //'profile' => '',
    //'search' => '',
    //'subscribe' => '',
    //'goto' => '',
);
$conf['rb_act_users'] = array(
    //'admin' => '',
    'backlink' => '@ALL',
    // Always show edit to users of the groups 'reviewers' and 'editors'
    // If they don't have edit level access, they will see "Show page source"
    'edit' => '@review,@edit',
    'revert' => '@edit',
    'history' => '@review,@edit',
    'recent' => '@review,@edit',
    // Everybody should be able to see the index
    'index' => '@ALL',
    // and the Login button/link
    'login' => '@ALL',
    // and the "Update Profile" form once 1logged in
    'profile' => '@ALL',
    // everybody in the standard group should see the "(Un)Subscribe" action
    'subscribe' => '@user',
    // Everybody can use the search form
    'search' => '@ALL',
    // but only registered users see the "Goto" form
    'goto' => '@user',
);
?>
