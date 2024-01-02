<?php
/**
 * ChildMetadataHelper class
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace zero24\Helper;

use zero24\Helper\FileHelper;

/**
 * Class for ChildMetadataHelper
 *
 * @since  1.0
 */
class ChildMetadataHelper
{
    /**
     * The filename with the data files
     *
     * @var    string
     * @since  1.0
     */
    private $fileName = 'child_metadata';

    /**
     * The FileHelper object pointing to the data folder
     *
     * @var    FileHelper
     * @since  1.0
     */
    private $fileHelper;

    /**
     * Constructor.
     *
     * @param   array  $options  Options to init the connection
     *
     * @since   1.0
     */
    public function __construct($options)
    {
        $this->fileHelper = new FileHelper([
            'dataFolder' => $options['dataFolder'],
        ]);

        $this->fileName = $options['fileName'];
    }

    /**
     * Get all childs from the text mapping file
     *
     * @return  string  Decoded JSON object with all childs information
     *
     * @since   1.0
     */
    public function getChilds()
    {
        return $this->fileHelper->readJsonFile($this->fileName);
    }

    /**
     * Get next child id
     *
     * @return  string  Next free child ID
     *
     * @since   1.0
     */
    public function getNextChildId()
    {
        $childs = $this->getChilds();

        foreach ($childs as $child)
        {
            $lastId = $child['child_id'];
        }

        // Increase the last point Id by one
        $lastId++;

        return $lastId;
    }

    /**
     * Get one specific child by child ID
     *
     * @param   string  $childId  child ID
     *
     * @return  object|false  Decoded JSON object with the requested child information or false
     *
     * @since   1.0
     */
    public function getChildById($childId)
    {
        $childs = $this->getChilds();

        foreach ($childs as $child)
        {
            if ($child['child_id'] === $childId)
            {
                return $child;
            }
        }

        return false;
    }


    /**
     * Get one specific child by UUID
     *
     * @param   string  $childUUID  child UUID
     *
     * @return  object|false  Decoded JSON object with the requested child information or false
     *
     * @since   1.0
     */
    public function getChildByUUID($childUUID)
    {
        $childs = $this->getChilds();

        foreach ($childs as $child)
        {
            if ($child['uuid'] === $childUUID)
            {
                return $child;
            }
        }

        return false;
    }

    /**
     * Check whether the given child UUID is valid
     *
     * @param   string  $childUUID  Child UUID
     *
     * @return  boolean  True or false whether the UUID exists or not
     *
     * @since   1.0
     */
    public function isValidUUID($childUUID)
    {
        if ($this->getChildByUUID($childUUID))
        {
            return true;
        }

        return false;
    }

    /**
     * Add a new child to the json file
     *
     * @param   array  $data  child data posted to the app
     *
     * @return  array  child data posted to the app
     *
     * @since   1.0
     */
    public function createChild($data)
    {
        $childs   = $this->getChilds();
        $childs[] = $data;

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $childs,
                JSON_PRETTY_PRINT
            )
        );

        return $data;
    }

    /**
     * Edit an existing child from the json file
     *
     * @param   array   $data     New child data posted to the app
     * @param   string  $childId  child ID that should be edited
     *
     * @return  array  child data posted to the app
     *
     * @since   1.0
     */
    public function editChild($data, $childId)
    {
        $editChild = [];
        $childs    = $this->getChilds();

        foreach ($childs as $i => $child)
        {
            if ($childs['child_id'] === $childId)
            {
                $childs[$i] = $editChild = array_merge($child, $data);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $childs,
                JSON_PRETTY_PRINT
            )
        );

        return $editChild;
    }

    /**
     * Delete an existing child from the json file
     *
     * @param   string  child ID to be deleted
     *
     * @return  void
     *
     * @since   1.0
     */
    public function deleteChild($childId)
    {
        $childs = $this->getChilds();

        foreach ($childs as $i => $child)
        {
            if ($childs['child_id'] === $childId)
            {
                array_splice($childs, $i, 1);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $childs,
                JSON_PRETTY_PRINT
            )
        );
    }

    /**
     * Validate the child data passed to the app
     *
     * @param   array   &$child   The child data to be validated (referenced)
     * @param   array   &$errors  The errors collected while validating (referenced)
     * @param   string  $type     String wether we are in 'edit' or 'create' mode
     *
     * @return  bool
     *
     * @since   1.0
     */
    public function validateChild(&$child, &$errors, $type)
    {
        $isValid = true;

        // Start of validation
        if (!$child['child_id'] || ($type === 'create' && $this->getChildById($child['child_id'])))
        {
            $isValid = false;
            $errors['child_id'] = 'Child ID is mandatory and has to be unique';
        }

        if (!$child['firstname'] || !is_string($child['firstname']))
        {
            $isValid = false;
            $errors['firstname'] = 'The First name is mandatory and has to be a string';
        }

        if (!$child['lastname'] || !is_string($child['lastname']))
        {
            $isValid = false;
            $errors['lastname'] = 'The First name is mandatory and has to be a string';
        }

        if (!$child['uuid'] || ($type === 'create' && $this->isValidUUID($child['uuid'])))
        {
            $isValid = false;
            $errors['uuid'] = 'UUID is mandatory and has to be unique';
        }

        return $isValid;
    }
}
