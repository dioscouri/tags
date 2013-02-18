<?php
/**
 * @version 1.5
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Tags::load( 'TagsViewBase', 'views._base', array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_tags' ) );

class TagsViewTags extends TagsViewBase 
{
    /**
     * Basic commands for displaying a list
     *
     * @param $tpl
     * @return unknown_type
     */
    function _default($tpl='')
    {
        Tags::load( 'TagsSelect', 'library.select' );
        Tags::load( 'TagsGrid', 'library.grid' );
        $model = $this->getModel( 'Tag' );

        // set the model state
            $state = $model->getState();
            JFilterOutput::objectHTMLSafe( $state );
            $this->assign( 'state', $state );

        // page-navigation
            $this->assign( 'pagination', $model->getPagination() );

        // list of items
            $this->assign('items', $model->getList());

        // form
            $validate = JUtility::getToken();
            $form = array();
            $view = strtolower( JRequest::getVar('view') );
            $form['action'] = "index.php?option=com_tags&controller={$view}&view={$view}";
            $form['validate'] = "<input type='hidden' name='{$validate}' value='1' />";
            $this->assign( 'form', $form );
    }
}