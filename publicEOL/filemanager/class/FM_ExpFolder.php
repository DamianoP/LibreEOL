<?php

/**
 * This code is part of the FileManager software (www.gerd-tentler.de/tools/filemanager), copyright by
 * Gerd Tentler. Obtain permission before selling this code or hosting it on a commercial website or
 * redistributing it over the Internet or in any other medium. In all cases copyright must remain intact.
 */

/**
 * This is the data structure of a single explorer folder.
 *
 * @package FileManager
 * @subpackage class
 * @author Gerd Tentler
 */
class FM_ExpFolder {

/* PUBLIC PROPERTIES *************************************************************************** */

	/**
	 * folder level
	 *
	 * @var integer
	 */
	public $level;

	/**
	 * full path
	 *
	 * @var string
	 */
	public $path;

	/**
	 * last update timestamp
	 *
	 * @var integer
	 */
	public $time;

	/**
	 * number of files
	 *
	 * @var integer
	 */
	public $files;

	/**
	 * folder size in bytes
	 *
	 * @var integer
	 */
	public $size;

/* PUBLIC METHODS ****************************************************************************** */

	/**
	 * constuctor
	 *
	 * @param integer $level		folder level
	 * @param string $path			full path
	 * @param integer $files		number of files
	 * @param integer $size			folder size in bytes
	 * @return FM_ExpFolder
	 */
	public function  __construct($level, $path, $files, $size) {
		$this->level = $level;
		$this->path = $path;
		$this->files = $files;
		$this->size = $size;
		$this->time = time();
	}
}

?>
