<?php
/**
 * @version	0.1
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

	jimport('joomla.form.formfield');
	jimport( 'joomla.html.parameter.element' );
	class JFormFieldScopesmultiple extends JFormField {

		var	$type = 'item';

		function getInput(){
			return JElementScopesmultiple::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}


	}




class JElementScopesmultiple extends JElement
{

	var	$_name = 'scopesmultiple';

	function fetchElement($name, $value, &$node, $control_name){
		$db = &JFactory::getDBO();

		$query = 'SELECT m.* FROM #__tags_scopes m ORDER BY scope_name';
		$db->setQuery( $query );
		$list = $db->loadObjectList();
		$mitems = array();

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->scope_id, $item->scope_name );
		}
		
		$doc = & JFactory::getDocument();
		$js = "
		window.addEvent('domready', function(){
			
			$('paramscatfilter0').addEvent('click', function(){
				$('paramscategory_id').setProperty('disabled', 'disabled');
				$$('#paramscategory_id option').each(function(el) {
					el.setProperty('selected', 'selected');
				});
			})
			
			$('paramscatfilter1').addEvent('click', function(){
				$('paramscategory_id').removeProperty('disabled');
				$$('#paramscategory_id option').each(function(el) {
					el.removeProperty('selected');
				});

			})
			
			if ($('paramscatfilter0').checked) {
				$('paramscategory_id').setProperty('disabled', 'disabled');
				$$('#paramscategory_id option').each(function(el) {
					el.setProperty('selected', 'selected');
				});
			}
			
			if ($('paramscatfilter1').checked) {
				$('paramscategory_id').removeProperty('disabled');
			}
			
		});
		";
		
		$doc->addScriptDeclaration($js);
		$output= JHTML::_('select.genericlist',  $mitems, ''.$control_name.$name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $value );
		return $output;
	}
}
