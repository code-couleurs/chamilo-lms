<?php
/* For licensing terms, see /license.txt */

/**
 *	Hidden frame that refreshes the visible frames when a modification occurs
 *
 *	@author Olivier Brouckaert
 *	@package chamilo.chat
 */
use ChamiloSession as Session;

define('FRAME', 'hidden');

require_once '../inc/global.inc.php';
require_once 'chat_functions.lib.php';

$tbl_user = Database::get_main_table(TABLE_MAIN_USER);
$tbl_chat_connected = Database::get_course_table(TABLE_CHAT_CONNECTED);
$userId = api_get_user_id();
$course_id = api_get_course_int_id();

$query = "SELECT username FROM $tbl_user WHERE user_id='".$userId."'";
$result = Database::query($query);

list($pseudo_user) = Database::fetch_row($result);

$isAllowed = !(empty($pseudo_user) || !$_cid);
$isMaster = (bool)api_is_course_admin();

$date_now = date('Y-m-d');

$group_id = api_get_group_id();
$session_id = api_get_session_id();
$session_condition = api_get_session_condition($session_id);
$group_condition = " AND to_group_id = '$group_id'";

$extra_condition = '';
if (!empty($group_id)) {
	$extra_condition = $group_condition;
} else {
	$extra_condition = $session_condition;
}

$extra_condition.= " AND c_id = $course_id";

// get chat path
$chat_path = '';
$document_path = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';
if (!empty($group_id)) {
	$group_info = GroupManager :: get_group_properties($group_id);
	$chat_path = $document_path.$group_info['directory'].'/chat_files/';
} else {
	$chat_path = $document_path.'/chat_files/';
}

// get chat file
$basename_chat = '';
if (!empty($group_id)) {
	$basename_chat = 'messages-'.$date_now.'_gid-'.$group_id;
} elseif (!empty($session_id)) {
	$basename_chat = 'messages-'.$date_now.'_sid-'.$session_id;
} else {
	$basename_chat = 'messages-'.$date_now;
}

$chat_size_old = isset($_POST['chat_size_old']) ? intval($_POST['chat_size_old']) : null;

$file = $chat_path.$basename_chat.'.log.html';
$chat_size_new = 0;
if (file_exists($file)) {
    $chat_size_new = filesize($file);
}

$sql = "SELECT user_id FROM $tbl_chat_connected
        WHERE user_id='".$userId."' $extra_condition";
$result = Database::query($sql);

// The user_id exists so we must do an UPDATE and not a INSERT
$current_time = api_get_utc_datetime();

if (Database::num_rows($result) == 0) {
	$query = "INSERT INTO $tbl_chat_connected(c_id, user_id, last_connection,session_id,to_group_id)
	          VALUES($course_id, '".$userId."','$current_time','$session_id','$group_id')";
    Database::query($query);
    $id = Database::insert_id();
    if ($id) {
        $sql = "UPDATE $tbl_chat_connected SET id = iid WHERE iid = $id";
        Database::query($sql);
    }
} else {
	$query = "UPDATE $tbl_chat_connected SET
	          last_connection='".$current_time."'
	          WHERE c_id = $course_id AND user_id='".$userId."' AND session_id='$session_id' AND to_group_id='$group_id'";
    Database::query($query);
}

$query = "SELECT COUNT(user_id) FROM $tbl_chat_connected
          WHERE last_connection>'".date('Y-m-d H:i:s',time()-60*5)."' $extra_condition";
$result = Database::query($query);

$connected_old = isset($_POST['connected_old']) ? intval($_POST['connected_old']) : null;
list($connected_new) = Database::fetch_row($result);

/*disconnected user of chat*/
disconnect_user_of_chat();
require 'header_frame.inc.php';
?>
<form name="formHidden" method="post" action="<?php echo api_get_self().'?'.api_get_cidreq(); ?>">
<input type="hidden" name="chat_size_old" value="<?php echo $chat_size_new; ?>">
<input type="hidden" name="connected_old" value="<?php echo $connected_new; ?>">
</form>
<?php
$origin = Session::read('origin');
if ($origin == 'whoisonline') {
    //check if our target has denied our request or not
    $talk_to = Session::read('target');
    $track_user_table = Database::get_main_table(TABLE_MAIN_USER);
    $sql = "select chatcall_text from $track_user_table where ( user_id = $talk_to )";
    $result = Database::query($sql);
    $row = Database::fetch_array($result);
    if ($row['chatcall_text'] == 'DENIED') {
        echo "<script language=\"javascript\" type=\"text/javascript\"> alert('".get_lang('ChatDenied')."'); </script>";
        $sql = "update $track_user_table set chatcall_user_id = '', chatcall_date = '', chatcall_text='' WHERE (user_id = $talk_to)";
        $result = Database::query($sql);
    }
}
require 'footer_frame.inc.php';
