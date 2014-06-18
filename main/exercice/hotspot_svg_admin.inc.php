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
	#hotspots tr, a {
		cursor: pointer;
	}
	#draw_menu li {
		float: left;
	}
	a.hotspot-selector {
		width: 15px;
		height: 15px;
		display: block;
	}
	#hotspots tr.active a.hotspot-selector {
		width: 20px;
		height: 20px;
	}
</style>

<a class="add_hotspot">Add hotspot</a>
<table id="hotspots">
	<tr>
		<th></th>
		<th><?php echo get_lang('Hotspot') ?></th>
		<th><?php echo get_lang('Comment') ?></th>
		<th><?php echo get_lang('QuestionWeighting') ?></th>
	</tr>
</table>

Tools :
<ul id="draw_menu">
	<li class="clear_hotspot"><a>Clear hotspot</a></li>
	<li class="geometry-type">
		<select class="choose_geometry">
			<option value="polygon">Polygone</option>
			<option value="ellipse">Ellipse</option>
			<option value="rectangle">Rectangle</option>
		</select>
	</li>
</ul>
<div class="clear"></div>
<div id="paper" style="background: url(<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$objQuestion->picture; ?>); width:1000px; height: 1000px; background-repeat: no-repeat"></div>


<script type="text/template" id="tpl_form_hotspot_row">
	<tr id="{hotspot_id}" class="hotspot">
		<td>
			<a class="hotspot-selector" style="background-color:{hotspot_color}"></a>
		</td>
		<td class="hotspot-title">
			<input type="text" name="reponse[{hotspot_inc}]" size="45" />
		</td>
		<td class="hotspot-comment">
			<textarea name="comment"></textarea>
		</td>
		<td class="hotspot-score">
			<input type="text" name="weighting[{hotspot_inc}]" value="10.00">
		</td>
		<td class="hotspot-actions">
			<a class="hotspot-delete"><?php echo Display::display_icon('delete.png') ?></a>
		</td>
	</tr>
</script>