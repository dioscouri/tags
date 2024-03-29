<?php
/**
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Tags::load('TagsViewBase', 'views._base');
class TagsViewConfig extends TagsViewBase 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function getLayoutVars($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
	}
	
	/**
	 * 
	 * @return void
	 **/
	function _default($tpl = null) 
	{
		
		// check config
			$row = TagsConfig::getInstance();
			$this->assign( 'row', $row );
		
		// add toolbar buttons
			JToolBarHelper::save('save');
			JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
			
		// plugins
        	$filtered = array();
	        $items = DSCTools::getPlugins();
			for ($i=0; $i<count($items); $i++) 
			{
				$item = $items[$i];
				// Check if they have an event
				if ($hasEvent = DSCTools::hasEvent( $item, 'onListConfigTags' )) {
					// add item to filtered array
					$filtered[] = $item;
				}
			}
			$items = $filtered;
			$this->assign( 'items_sliders', $items );
			
		// Add pane
			jimport('joomla.html.pane');
			$sliders = JPane::getInstance( 'sliders' );
			$this->assign('sliders', $sliders);
			
		// form
			$validate = JUtility::getToken();
			$form = array();
			$view = strtolower( JRequest::getVar('view') );
			$form['action'] = "index.php?option=com_tags&controller={$view}&view={$view}";
			$form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
			$this->assign( 'form', $form );
			
		// set the required image
		// TODO Fix this to use defines
			$required = new stdClass();
			$required->text = JText::_( 'Required' );
			$required->image = "<img src='".JURI::root()."/media/com_tags/images/required_16.png' alt='{$required->text}'>";
			$this->assign('required', $required );
			
		// Elements
		$elementArticleModel 	= JModel::getInstance( 'ElementArticle', 'TagsModel' );
		$this->assign( 'elementArticleModel', $elementArticleModel );
		
			// terms
			$elementArticle_terms 		= $elementArticleModel->_fetchElement( 'article_terms', @$row->get('article_terms') );
			$resetArticle_terms			= $elementArticleModel->_clearElement( 'article_terms', '0' );
			$this->assign('elementArticle_terms', $elementArticle_terms);
			$this->assign('resetArticle_terms', $resetArticle_terms);
            // shipping
            $elementArticle_shipping       = $elementArticleModel->_fetchElement( 'article_shipping', @$row->get('article_shipping') );
            $resetArticle_shipping         = $elementArticleModel->_clearElement( 'article_shipping', '0' );
            $this->assign('elementArticle_shipping', $elementArticle_shipping);
            $this->assign('resetArticle_shipping', $resetArticle_shipping);			
			

    }
    
}
