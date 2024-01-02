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
class SessionHelper
{
    /**
     * The filename with the data files
     *
     * @var    string
     * @since  1.0
     */
    private $fileName = 'session';

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
     * Get all sessions from the json file
     *
     * @return  string  Decoded JSON object with all session information
     *
     * @since   1.0
     */
    public function getSessions()
    {
        return $this->fileHelper->readJsonFile($this->fileName);
    }

    /**
     * Get one specific session by session ID
     *
     * @param   string  $sessionId  child ID
     *
     * @return  object|false  Decoded JSON object with the requested child information or false
     *
     * @since   1.0
     */
    public function getSessionById($sessionId)
    {
        $sessions = $this->getSessions();

        foreach ($sessions as $session)
        {
            if ($session['session_id'] === $sessionId)
            {
                return $session;
            }
        }

        return false;
    }

    /**
     * Add a new session to the json file
     *
     * @param   array  $data  session data posted to the app
     *
     * @return  array  session data posted to the app
     *
     * @since   1.0
     */
    public function createSession($data)
    {
        $sessions   = $this->getSessions();
        $sessions[] = $data;

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $sessions,
                JSON_PRETTY_PRINT
            )
        );

        return $data;
    }

    /**
     * Edit an existing session from the json file
     *
     * @param   array   $data       New session data posted to the app
     * @param   string  $sessionId  session ID that should be edited
     *
     * @return  array  session data posted to the app
     *
     * @since   1.0
     */
    public function editSession($data, $sessionId)
    {
        $editSession = [];
        $sessions    = $this->getSessions();

        foreach ($sessions as $i => $session)
        {
            if ($session['session_id'] === $sessionId)
            {
                $sessions[$i] = $editSession = array_merge($session, $data);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $sessions,
                JSON_PRETTY_PRINT
            )
        );

        return $editSession;
    }

    /**
     * Delete an existing session from the json file
     *
     * @param   string  session ID to be deleted
     *
     * @return  void
     *
     * @since   1.0
     */
    public function deleteSession($sessionId)
    {
        $sessions = $this->getSessions();

        foreach ($sessions as $i => $session)
        {
            if ($session['session_id'] === $sessionId)
            {
                array_splice($sessions, $i, 1);
            }
        }

        $this->fileHelper->writeJsonFile(
            $this->fileName,
            json_encode(
                $sessions,
                JSON_PRETTY_PRINT
            )
        );
    }

    /**
     * Validate the session data passed to the app
     *
     * @param   array   &$session  The session data to be validated (referenced)
     * @param   array   &$errors   The errors collected while validating (referenced)
     * @param   string  $type      String wether we are in 'edit' or 'create' mode
     *
     * @return  bool
     *
     * @since   1.0
     */
    public function validateSession(&$session, &$errors, $type)
    {
        $isValid = true;

        // Start of validation
        if (!$session['session_id'] || ($type === 'create' && $this->getSessionById($session['session_id'])))
        {
            $isValid = false;
            $errors['session_id'] = 'Session ID is mandatory and has to be unique';
        }

        if (!$session['canCreate'] || !in_array($session['canCreate'], ['true', 'false']))
        {
            $isValid = false;
            $errors['canCreate'] = 'The canCreate value is mandatory and has to be "true" or "false"';
        }

        if (!$session['canRead'] || !in_array($session['canRead'], ['true', 'false']))
        {
            $isValid = false;
            $errors['canRead'] = 'The canRead value is mandatory and has to be "true" or "false"';
        }

        if (!$session['canUpdate'] || !in_array($session['canUpdate'], ['true', 'false']))
        {
            $isValid = false;
            $errors['canUpdate'] = 'The canUpdate value is mandatory and has to be "true" or "false"';
        }

        if (!$session['canDelete'] || !in_array($session['canDelete'], ['true', 'false']))
        {
            $isValid = false;
            $errors['canDelete'] = 'The canDelete value is mandatory and has to be "true" or "false"';
        }

        if (!$session['isAdmin'] || !in_array($session['isAdmin'], ['true', 'false']))
        {
            $isValid = false;
            $errors['isAdmin'] = 'The isAdmin value is mandatory and has to be "true" or "false"';
        }

        return $isValid;
    }

    /**
     * Generate a new UUID v4
     *
     * @return  string
     *
     * @since   1.0
     * @see     https://www.uuidgenerator.net/dev-corner/php
     */
    public function generateNewUUIDv4(): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        $newUUID = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

        // Double check that we are not reusing an UUID
        if ($this->getSessionById($newUUID))
        {
            return $this->generateNewUUIDv4();
        }

        return $newUUID;
    }
}
