<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    30th April, 2015
 * @author     Llewellyn van der Merwe <http://www.joomlacomponentbuilder.com>
 * @github     Joomla Component Builder <https://github.com/vdm-io/Joomla-Component-Builder>
 * @copyright  Copyright (C) 2015 - 2020 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Helpdocumentsfilterlocation Form Field class for the Componentbuilder component
 */
class JFormFieldHelpdocumentsfilterlocation extends JFormFieldList
{
	/**
	 * The helpdocumentsfilterlocation field type.
	 *
	 * @var		string
	 */
	public $type = 'helpdocumentsfilterlocation';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('location'));
		$query->from($db->quoteName('#__componentbuilder_help_document'));
		$query->order($db->quoteName('location') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get help_documentsmodel
			$model = ComponentbuilderHelper::getModel('help_documents');
			$results = array_unique($results);
			$_filter = array();
			$_filter[] = JHtml::_('select.option', '', '- ' . JText::_('COM_COMPONENTBUILDER_FILTER_SELECT_LOCATION') . ' -');
			foreach ($results as $location)
			{
				// Translate the location selection
				$text = $model->selectionTranslation($location,'location');
				// Now add the location and its text to the options array
				$_filter[] = JHtml::_('select.option', $location, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}
}