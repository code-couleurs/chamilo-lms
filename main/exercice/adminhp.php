<?php
/* For licensing terms, see /license.txt */
/**
*	HotPotatoes administration.
*	@package chamilo.exercise
* 	@author Istvan Mandak
* 	@version $Id: adminhp.php 20089 2009-04-24 21:12:54Z cvargas1 $
*/
require_once '../inc/global.inc.php';

$this_section=SECTION_COURSES;

if (isset($_REQUEST["cancel"])) {
    if ($_REQUEST["cancel"]==get_lang('Cancel')) {
        header("Location: exercise.php");
    }
}

//$is_courseAdmin = $_SESSION['is_courseAdmin'];
$newName = (!empty($_REQUEST['newName'])?$_REQUEST['newName']:'');
$hotpotatoesName = (!empty($_REQUEST['hotpotatoesName'])?$_REQUEST['hotpotatoesName']:'');

$is_allowedToEdit=api_is_allowed_to_edit(null,true);

// document path
$documentPath=api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';

// picture path
$picturePath=$documentPath.'/images';

// audio path
$audioPath=$documentPath.'/audio';

// Database table definitions
if (!$is_allowedToEdit) {
    api_not_allowed(true);
}

if (api_is_in_gradebook()) {
    $interbreadcrumb[]= array(
        'url' => api_get_path(WEB_CODE_PATH).'gradebook/index.php?'.api_get_cidreq(),
        'name' => get_lang('ToolGradebook')
    );
}

$interbreadcrumb[] = array(
    "url" => "exercise.php?".api_get_cidreq(),
    "name" => get_lang('Exercises'),
);

$nameTools = get_lang('adminHP');

Display::display_header($nameTools,"Exercise");

/** @todo probably wrong !!!! */
require_once(api_get_path(SYS_CODE_PATH).'/exercice/hotpotatoes.lib.php');

?>

<h4>
  <?php echo $nameTools; ?>
</h4>

<?php
if (isset($newName)) {
    if ($newName!="") {
        //alter database record for that test
        SetComment($hotpotatoesName,$newName);
        echo "<script language='Javascript' type='text/javascript'> window.location='exercise.php'; </script>";
    }
}

echo "<form action=\"".api_get_self()."\" method='post' name='form1'>";
echo "<input type=\"hidden\" name=\"hotpotatoesName\" value=\"$hotpotatoesName\">";
echo "<input type=\"text\" name=\"newName\" value=\"";


$lstrComment = "";
$lstrComment = GetComment($hotpotatoesName);
if ($lstrComment=="") {
    $lstrComment = GetQuizName($hotpotatoesName,$documentPath);
}
if ($lstrComment=="") {
    $lstrComment = basename($hotpotatoesName,$documentPath);
}

echo $lstrComment;
echo "\" size=40>&nbsp;";
echo "<button type=\"submit\" class=\"save\" name=\"submit\" value=\"".get_lang('Ok')."\">".get_lang('Ok')."</button>";
echo "<button type=\"button\" class=\"cancel\" name=\"cancel\" value=\"".get_lang('Cancel')."\" onclick=\"javascript:document.form1.newName.value='';\">".get_lang('Cancel')."</button>";
echo "</form>";

Display::display_footer();
