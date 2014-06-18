<?php
/* For licensing terms, see /license.txt */
/**
 * This script aims at replacing the old swf hotspot admin by a comination of javascript and svg
 * @package chamilo.exercise
 * @author Eric Marguin
 */

// TODO there seems to be a security lack here
$modifyAnswers = intval($_GET['hotspotadmin']);

if (!is_object($objQuestion)) {
    $objQuestion = Question :: read($modifyAnswers);
}
?>

<!-- TODO minimize it -->
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH) ?>raphael-min.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/common.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/geometry.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/polygon.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/ellipse.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/rectangle.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/hotspot.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/objects/hotspotcollection.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH)?>hotspot/admin.js"></script>

<!-- Temporary drafts styles for dev -->
<style>
	#hotspots li, a {
		cursor: pointer;
	}
	#hotspots li.active {
		font-weight: bold;
	}
	#draw_menu li {
		float: left;
	}
</style>

<a class="add_hotspot">Add hotspot</a>
<ul id="hotspots">
</ul>

Tools :
<ul id="draw_menu">
	<li class="clear_hotspot"><a>Clear hotspot</a></li>
</ul>
<div class="clear"></div>
<div id="paper" style="background: url(<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$objQuestion->picture; ?>); width:1000px; height: 1000px; background-repeat: no-repeat"></div>