<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.3.8
	@build			10th March, 2016
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		company.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@owner			Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
/-------------------------------------------------------------------------------------------------------/
	Cost Benefit Projection Tool.
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Company Form Field class for the Costbenefitprojection component
 */
class JFormFieldCompany extends JFormFieldList
{
	/**
	 * The company field type.
	 *
	 * @var		string
	 */
	public $type = 'company'; 
	/**
	 * Override to add new button
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.2
	 */
	protected function getInput()
	{
		// see if we should add buttons
		$setButton = $this->getAttribute('button');
		// get html
		$html = parent::getInput();
		// if true set button
		if ($setButton === 'true')
		{
			$user = JFactory::getUser();
			// only add if user allowed to create company
			if ($user->authorise('company.create', 'com_costbenefitprojection'))
			{
				// get the input from url
				$jinput = JFactory::getApplication()->input;
				// get the view name & id
				$values = $jinput->getArray(array(
					'id' => 'int',
					'view' => 'word'
				));
				// check if new item
				$ref = '';
				if (!is_null($values['id']) && strlen($values['view']))
				{
					// only load referal if not new item.
					$ref = '&amp;ref=' . $values['view'] . '&amp;refid=' . $values['id'];
				}
				// build the button
				$button = '<a class="btn btn-small btn-success"
					href="index.php?option=com_costbenefitprojection&amp;view=company&amp;layout=edit'.$ref.'" >
					<span class="icon-new icon-white"></span>' . JText::_('COM_COSTBENEFITPROJECTION_NEW') . '</a>';
				// return the button attached to input field
				return $html . $button;
			}
		}
		return $html;
	}

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	public function getOptions()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name'),array('id','company_name')));
		$query->from($db->quoteName('#__costbenefitprojection_company', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		if (!$user->authorise('core.options', 'com_costbenefitprojection'))
		{
			$companies = CostbenefitprojectionHelper::hisCompanies($user->id);
			if (CostbenefitprojectionHelper::checkArray($companies))
			{
				$companies = implode(',',$companies);
				// only load this users companies
				$query->where('a.id IN (' . $companies . ')');
			}
			else
			{
				// dont allow user to see any companies
				$query->where('a.id = -4');
			}
		}
		$query->order('a.name ASC');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			$userIs = CostbenefitprojectionHelper::userIs($user->id);
			if (3 == $userIs || $user->authorise('core.options', 'com_costbenefitprojection'))
			{
				$options[] = JHtml::_('select.option', 0, '-- '.JText::_('A Country').' --');
			}
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->id, $item->company_name);
			}
		}
		return $options;
	}
}