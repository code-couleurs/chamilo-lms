<?php
/* For licensing terms, see /license.txt */
/**
 * This script aims at replacing the old swf hotspot admin by a comination of javascript and svg
 * @package chamilo.exercise
 * @author Eric Marguin
 */

use \ChamiloSession as Session;

// TODO there seems to be a security lack here
$modifyAnswers = intval($_GET['hotspotadmin']);

$hotspot_admin_url = api_get_path(WEB_CODE_PATH) . 'exercice/admin.php?' . api_get_cidreq() . '&hotspotadmin=' . $modifyAnswers . '&exerciseId=' . $exerciseId;

if (!is_object($objQuestion)) {
    $objQuestion = Question :: read($modifyAnswers);
}
// construction of the Answer object
$objAnswer = new Answer($objQuestion->id);
Session::write('objAnswer', $objAnswer);

// if we come from the warning box "this question is used in serveral exercises"
if ($modifyIn) {
    if ($debug > 0) {
        echo '$modifyIn was set' . "<br />\n";
    }
    // if the user has chosed to modify the question only in the current exercise
    if ($modifyIn == 'thisExercise') {
        // duplicates the question
        $questionId = $objQuestion->duplicate();

        // deletes the old question
        $objQuestion->delete($exerciseId);

        // removes the old question ID from the question list of the Exercise object
        $objExercise->removeFromList($modifyAnswers);

        // adds the new question ID into the question list of the Exercise object
        $objExercise->addToList($questionId);

        // construction of the duplicated Question
        $objQuestion = Question :: read($questionId);

        // adds the exercise ID into the exercise list of the Question object
        $objQuestion->addToList($exerciseId);

        // copies answers from $modifyAnswers to $questionId
        $objAnswer->duplicate($questionId);

        // construction of the duplicated Answers

        $objAnswer = new Answer($questionId);
    }
    $color = unserialize($color);
    $reponse = unserialize($reponse);
    $comment = unserialize($comment);
    $weighting = unserialize($weighting);
    $hotspot_coordinates = unserialize($hotspot_coordinates);
    $hotspot_type = unserialize($hotspot_type);
    $destination = unserialize($destination);
    unset($buttonBack);
}

// form has been submitted
if (!empty($_POST['formSent'])) {
	
	$questionWeighting = $nbrGoodAnswers = 0;
	$answers = $_POST['answers'];
	$comments = $_POST['comments'];
	$weightings = $_POST['weightings'];
	$hotspot_coordinates = $_POST['hotspot_coordinates'];
	$hotspot_types = $_POST['hotspot_types'];
	for ($i = 1; $i <= count($answers); $i++) {
		
		// checks if field is empty
		if (empty($answers[$i]) && $answers[$i] != '0') {
			$msgErr = get_lang('HotspotGiveAnswers');

			// clears answers already recorded into the Answer object
			$objAnswer->cancel();
			break;
		}

		if ($weightings[$i] <= 0) {
			$msgErr = get_lang('HotspotWeightingError');
			// clears answers already recorded into the Answer object
			$objAnswer->cancel();
			break;
		}

		if ($hotspot_coordinates[$i] == '0;0|0|0' || empty($hotspot_coordinates[$i])) {
			$msgErr = get_lang('HotspotNotDrawn');
			// clears answers already recorded into the Answer object
			$objAnswer->cancel();
			break;
		}
	}  // end for()

	if (empty($msgErr)) {
		for ($i = 1; $i <= count($answers); $i++) {
                if ($debug > 0) {
                    echo str_repeat('&nbsp;', 4) . '$answerType is HOT_SPOT' . "<br />\n";
                }
                $answers[$i] = trim($answers[$i]);
                $comments[$i] = trim($comments[$i]);
				$hotspot_types[$i] = trim($hotspot_types[$i]);
                if ($weightings[$i]) {
                    $questionWeighting+=$weightings[$i];
                }
                // creates answer
                $objAnswer->createAnswer($answers[$i], '', $comments[$i], $weightings[$i], $i, $hotspot_coordinates[$i], $hotspot_types[$i]);
            }  // end for()
            // saves the answers into the data base
            $objAnswer->save();

            // sets the total weighting of the question
            $objQuestion->updateWeighting($questionWeighting);
            $objQuestion->save($exerciseId);
	}
}
else { // form has not been submitted, get answers in db
	$i = 0;
	$answers = $comments = $weightings = $hotspot_coordinates = $hotspot_types = array();
	foreach ($objAnswer->answer as $answer_id =>  $answer_item) {
		$answers[++$i] = $objAnswer->selectAnswer($answer_id);
		$weightings[$i] = $objAnswer->selectWeighting($answer_id);
		$comments[$i] = $objAnswer->selectComment($answer_id);
		$hotspot_coordinates[$i] = $objAnswer->selectHotspotCoordinates($answer_id);
		$hotspot_types[$i] = $objAnswer->selectHotspotType($answer_id);
	}
}



?>

<!-- TODO minimize it -->
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH) ?>javascript/raphael-min.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/common.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/geometry.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/polygon.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/ellipse.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/rectangle.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/hotspot.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/objects/hotspotcollection.js"></script>
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_PATH)?>javascript/hotspot/admin.js"></script>

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

<?php if (!empty($msgErr)): ?>
<?php Display::display_normal_message($msgErr); ?>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function(){
	var hotspot_admin = new ChamiloHotspotAdmin();
	
	<?php if(!empty($answers) && count($answers)>0): ?>
	<?php foreach($answers as $i=>$answer): ?>
	hotspot_admin.add_hotspot('<?php echo Security::remove_XSS($answer) ?>','<?php echo Security::remove_XSS($comments[$i]) ?>', '<?php echo Security::remove_XSS($weightings[$i]) ?>', '<?php echo Security::remove_XSS($hotspot_coordinates[$i]) ?>', '<?php echo Security::remove_XSS($hotspot_types[$i]) ?>');
	<?php endforeach; ?>
	<?php endif; ?>
});
</script>
	

<form method="post">
	
	<button type="submit" class="btn save" name="submitAnswers" value="<?php echo get_lang('Ok'); ?>" /><?php echo get_lang('AddQuestionToExercise'); ?></button>
	<input type="hidden" name="formSent" value="1" />

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
				<option value="poly">Polygone</option>
				<option value="circle">Ellipse</option>
				<option value="square">Rectangle</option>
			</select>
		</li>
	</ul>
	<div class="clear"></div>
	<div id="paper" style="background: url(<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$objQuestion->picture; ?>); width:1000px; height: 1000px; background-repeat: no-repeat"></div>

</form>

<script type="text/template" id="tpl_form_hotspot_row">
	<tr id="{hotspot_id}" class="hotspot">
		<td>
			<a class="hotspot-selector" style="background-color:{hotspot_color}"></a>
		</td>
		<td class="hotspot-title">
			<input type="text" name="answers[{hotspot_inc}]" size="45" value="{hotspot_answer}" />
		</td>
		<td class="hotspot-comment">
			<textarea name="comments[{hotspot_inc}]">{hotspot_comment}</textarea>
		</td>
		<td class="hotspot-score">
			<input type="text" name="weightings[{hotspot_inc}]" value="{hotspot_weighting}">
		</td>
		<td class="hotspot-actions">
			<a class="hotspot-delete"><?php echo Display::display_icon('delete.png') ?></a>
		</td>
		<input type="hidden" class="hotspot_coordinates" name="hotspot_coordinates[{hotspot_inc}]" value="">
		<input type="hidden" class="hotspot_types" name="hotspot_types[{hotspot_inc}]" value="">
	</tr>
</script>