<?php
/*Author Asare-Asiedu Ernest
Provide integration with Mpower mobile payment platform*/

if (! defined ( 'DIR_CORE' )) {
header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionMpower extends AController {

		public $data = array ();
		private $error = array ();
 }
