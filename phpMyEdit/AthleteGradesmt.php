<?php
/*
 * Mysql Ajax Table Editor
 *
 * Copyright (c) 2008 Chris Kitchen <info@mysqlajaxtableeditor.com>
 * All rights reserved.
 *
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://www.mysqlajaxtableeditor.com
 */
require_once('AthleteFinal.php');
require_once('php/lang/LangVars-en.php');
require_once('php/AjaxTableEditor.php');
class Example1 extends Common
{
	var $Editor;
	
	function displayHtml()
	{
		?>
			<br />
	
			<div align="left" style="position: relative;"><div id="ajaxLoader1"><img src="images/ajax_loader.gif" alt="Loading..." /></div></div>
			
			<br />
			
			<div id="historyButtonsLayer" align="left">
			</div>
	
			<div id="historyContainer">
				<div id="information">
				</div>
		
				<div id="titleLayer" style="padding: 2px; font-weight: bold; font-size: 18px; text-align: center;">
				</div>
		
				<div id="tableLayer" align="center">
				</div>
				
				<div id="recordLayer" align="center">
				</div>		
				
				<div id="searchButtonsLayer" align="center">
				</div>
			</div>
			
			<script type="text/javascript">
				trackHistory = false;
				var ajaxUrl = '<?php echo $_SERVER['PHP_SELF']; ?>';
				toAjaxTableEditor('update_html','');
			</script>
		<?php
	}

	function initiateEditor()
	{
		$tableColumns['AGradeID'] = array('display_text' => 'GradeID', 'perms' => 'TVQSXO');
		$tableColumns['MembershipID'] = array('display_text' => 'MembershipID', 'perms' => 'EVCTAXQSHO');
		$tableColumns['Membership'] = array('display_text' => 'Members', 'perms' => 'EVCTAXQSHO', 'join' => array('table' => 'Athlete', 'column' => 'MembershipID', 'display_mask' => "concat(Athlete.FirstName,' ',Athlete.MiddleInitial,' ',Athlete.Surname)", 'real_column' => 'MembershipID', 'type' => 'left'));
		//$tableColumns['Surname'] = array('display_text' => 'Last Name', 'perms' => 'EVCTAXQSHO');
		//$tableColumns['DateOfBirth'] = array('display_text' => 'Birthday', 'perms' => 'EVCTAXQSHO', 'display_mask' => 'date_format(DateOfBirth,"%d %M %Y")', 'calendar' => '%d %B %Y','col_header_info' => 'style="width: 100px;"'); 
		//$tableColumns['PaymentDate'] = array('display_text' => 'Payment Date', 'perms' => 'EVCTAXQSHO');
		//$tableColumns['Gender'] = array('display_text' => 'Gender', 'perms' => 'EVCTAXQSHO', 'select_array' => array('Male' => 'Male', 'Female' => 'Female'));
		//$tableColumns['TelNumber'] = array('display_text' => 'Tel. Number', 'perms' => 'EVCTAXQSHO');
		//$tableColumns['E-mail'] = array('display_text' => 'Email', 'perms' => 'EVCTAXQSHO','col_header_info' => 'style="width: 50px;"'); 
		$tableColumns['GradeID'] = array('display_text' => 'Grade', 'perms' => 'EVCTAXQSHO', 'join' => array('table' => 'Grade', 'column' => 'GradeID', 'display_mask' => "concat(Grade.Grade)", 'type' => 'left'));
		$tableColumns['SportID'] = array('display_text' => 'Sport', 'perms' => 'EVCTAXQSHO', 'join' => array('table' => 'Sport', 'column' => 'SportID', 'display_mask' => "concat(Sport.Sport)", 'type' => 'left'));
		//$tableColumns['Sports'] = array('display_text' => 'Sports', 'perms' => 'EVCTAXQSHO', 'join' => array('table' => 'AthleteGrades', 'column' => 'MembershipID', 'display_mask' => "concat(AthleteGrades.SportID)", 'real_column' => 'MembershipID', 'alias' => 'merge','type' => 'left'));
		//$tableColumns['Sportsname'] = array('display_text' => 'SName', 'perms' => 'EVCTAXQSHO', 'join' => array('table' => 'Sport', 'column' => 'SportID', 'display_mask' => "concat(Sport.Sport)", 'real_column' => 'MembershipID','type' => 'left'));

		 
		$tableName = 'AthleteGrades';
		$primaryCol = 'MembershipID';
		$errorFun = array(&$this,'logError');
		$permissions = 'EAVIDQCSX';
		
		$this->Editor = new AjaxTableEditor($tableName,$primaryCol,$errorFun,$permissions,$tableColumns);
		$this->Editor->setConfig('tableInfo','cellpadding="1" width="880" class="mateTable"');
		$this->Editor->setConfig('orderByColumn','MembershipID');
		$this->Editor->setConfig('addRowTitle','Add Athlete');
		$this->Editor->setConfig('editRowTitle','Edit Athlete');
		//$this->Editor->setConfig('iconTitle','Edit Employee');
	}
	
	
	function Example1()
	{
		if(isset($_POST['json']))
		{
			session_start();
			// Initiating lang vars here is only necessary for the logError, and mysqlConnect functions in Common.php. 
			// If you are not using Common.php or you are using your own functions you can remove the following line of code.
			$this->langVars = new LangVars();
			$this->mysqlConnect();
			if(ini_get('magic_quotes_gpc'))
			{
				$_POST['json'] = stripslashes($_POST['json']);
			}
			if(function_exists('json_decode'))
			{
				$data = json_decode($_POST['json']);
			}
			else
			{
				require_once('php/JSON.php');
				$js = new Services_JSON();
				$data = $js->decode($_POST['json']);
			}
			if(empty($data->info) && strlen(trim($data->info)) == 0)
			{
				$data->info = '';
			}
			$this->initiateEditor();
			$this->Editor->main($data->action,$data->info);
			if(function_exists('json_encode'))
			{
				echo json_encode($this->Editor->retArr);
			}
			else
			{
				echo $js->encode($this->Editor->retArr);
			}
		}
		else if(isset($_GET['export']))
		{
            session_start();
            ob_start();
            $this->mysqlConnect();
            $this->initiateEditor();
            echo $this->Editor->exportInfo();
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/x-msexcel");
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$this->Editor->tableName.'.csv"');
            exit();
        }
		else
		{
			$this->displayHeaderHtml();
			$this->displayHtml();
			$this->displayFooterHtml();
		}
	}
}
$lte = new Example1();
?>
