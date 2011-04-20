<?php
class FilesController extends Moo_Controller_Action 
{
	public function indexAction()
	{
		
	}
	
	public function getrootdirAction()
	{
		if ($this->getRequest()->isXmlHttpRequest())
		{
			$this->_helper->viewRenderer->setNoRender();
			$this->_helper->layout->disableLayout();		
			
			$root = 'C:\wamp\www\magazine\www\upload';
			
			$_POST['dir'] = urldecode($_POST['dir']);			
			$dirs = '';
			
			if( file_exists($root . $_POST['dir']) ) 
			{
				$files = scandir($root . $_POST['dir']);
				natcasesort($files);
				
				if( count($files) > 2 ) 
				{ 		
					$dirs .= "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					// All dirs
					foreach( $files as $file ) 
					{
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' 
							&& $file != '..' && is_dir($root . $_POST['dir'] . $file) ) 
						{
							$dirs .= "<li class=\"directory collapsed\">
								<a href=\"#\" rel=\"" 
								. htmlentities($_POST['dir'] . $file) . "/\">" 
								. htmlentities($file) . "</a></li>";
						}
					}
					// All files
					foreach( $files as $file ) 
					{
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' 
							&& $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) 
						{
							$ext = preg_replace('/^.*\./', '', $file);
							$dirs .= "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" 
								. htmlentities($_POST['dir'] . $file) . "\">" 
								. htmlentities($file) . "</a></li>";
						}
					}
					echo "</ul>";	
				}
			}

			$this->getResponse()
				->setHeader("Cache-Control", "no-cache, must-revalidate")
				->setHeader("Pragma", "no-cache")
				->setHeader("Content-type", "application/json;charset=utf-8")
				->setBody($dirs);				
		}	
	}
}
?>