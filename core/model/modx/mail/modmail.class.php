<?php
/*
 * MODx Revolution
 *
 * Copyright 2006-2010 by the MODx Team.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 */

/**
 * This file contains the modMail email service interface definition.
 * @package modx
 * @subpackage mail
 */
/**
 * Defines the interface for the modX email service.
 *
 * @abstract Implement a derivative of this class to define an actual email service implementation.
 * @package modx
 * @subpackage mail
 */
abstract class modMail {
    const MAIL_BODY = 'mail_body';
    const MAIL_BODY_TEXT = 'mail_body_text';
    const MAIL_CHARSET = 'mail_charset';
    const MAIL_CONTENT_TYPE = 'mail_content_type';
    const MAIL_ENCODING = 'mail_encoding';
    const MAIL_ENGINE = 'mail_engine';
    const MAIL_ENGINE_PATH = 'mail_engine_path';
    const MAIL_ERROR_INFO = 'mail_error_info';
    const MAIL_FROM = 'mail_from';
    const MAIL_FROM_NAME = 'mail_from_name';
    const MAIL_HOSTNAME = 'mail_hostname';
    const MAIL_LANGUAGE = 'mail_language';
    const MAIL_PRIORITY = 'mail_priority';
    const MAIL_READ_TO = 'mail_read_to';
    const MAIL_SENDER = 'mail_sender';
    const MAIL_SERVICE = 'mail_service';
    const MAIL_SMTP_AUTH = 'mail_smtp_auth';
    const MAIL_SMTP_HELO = 'mail_smtp_helo';
    const MAIL_SMTP_HOSTS = 'mail_smtp_hosts';
    const MAIL_SMTP_KEEPALIVE = 'mail_smtp_keepalive';
    const MAIL_SMTP_PASS = 'mail_smtp_pass';
    const MAIL_SMTP_PORT = 'mail_smtp_port';
    const MAIL_SMTP_PREFIX = 'mail_smtp_prefix';
    const MAIL_SMTP_SINGLE_TO = 'mail_smtp_single_to';
    const MAIL_SMTP_TIMEOUT = 'mail_smtp_timeout';
    const MAIL_SMTP_USER = 'mail_smtp_user';
    const MAIL_SUBJECT = 'mail_subject';

    /**
     * A reference to the modX instance communicating with this service instance.
     * @var modX
     */
    public $modx= null;
    /**
     * A collection of attributes defining all of the details of email communication.
     * @var array
     */
    public $attributes= array();
    /**
     * The mailer object responsible for implementing the modMail methods.
     * @var object
     */
    public $mailer= null;
    /**
     * A collection of all the current headers for the object.
     * @var array
     */
    public $headers= array();
    /**
     * An array of address types: to, cc, bcc, reply-to
     * @var array
     */
    public $addresses= array(
        'to' => array(),
        'cc' => array(),
        'bcc' => array(),
        'reply-to' => array(),
    );
    /**
     * An array of attached files
     * @var array
     */
    public $files= array();

    /**
     * Constructs a new instance of the modMail class.
     *
     * {@inheritdoc}
     */
    function __construct(modX &$modx, array $attributes= array()) {
        $this->modx= & $modx;
        if (!$this->modx->lexicon) {
            $this->modx->getService('lexicon','modLexicon');
        }
        $this->modx->lexicon->load('mail');
        $this->defaultAttributes = is_array($attributes) ? $attributes : array();
        $this->attributes= $this->getDefaultAttributes($attributes);
    }

    /**
     * Gets the default attributes for modMail based on system settings
     *
     * @param array An optional array of default attributes to override with
     * @return array An array of default attributes
     */
    public function getDefaultAttributes(array $attributes = array()) {
        $default = array();
        if ($this->modx->getOption('mail_use_smtp',false)) {
            $default[modMail::MAIL_ENGINE] = 'smtp';
            $default[modMail::MAIL_SMTP_AUTH] = $this->modx->getOption('mail_smtp_auth',null,false);
            $helo = $this->modx->getOption('mail_smtp_helo','');
            if (!empty($helo)) { $default[modMail::MAIL_SMTP_HELO] = $helo; }
            $default[modMail::MAIL_SMTP_HOSTS] = $this->modx->getOption('mail_smtp_hosts',null,'localhost');
            $default[modMail::MAIL_SMTP_KEEPALIVE] = $this->modx->getOption('mail_smtp_keepalive',null,false);
            $default[modMail::MAIL_SMTP_PASS] = $this->modx->getOption('mail_smtp_pass',null,'');
            $default[modMail::MAIL_SMTP_PORT] = $this->modx->getOption('mail_smtp_port',null,25);
            $default[modMail::MAIL_SMTP_PREFIX] = $this->modx->getOption('mail_smtp_prefix',null,'');
            $default[modMail::MAIL_SMTP_SINGLE_TO] = $this->modx->getOption('mail_smtp_single_to',null,false);
            $default[modMail::MAIL_SMTP_TIMEOUT] = $this->modx->getOption('mail_smtp_timeout',null,10);
            $default[modMail::MAIL_SMTP_USER] = $this->modx->getOption('mail_smtp_user',null,'');
        }
        $default[modMail::MAIL_CHARSET] = $this->modx->getOption('mail_charset',null,'UTF-8');
        $default[modMail::MAIL_ENCODING] = $this->modx->getOption('mail_encoding',null,'8bit');

        /* first start with this method default, then constructor passed-in default, then method passed-in attributes */
        return array_merge($default,$this->defaultAttributes,$attributes);
    }

    /**
     * Gets a reference to an attribute of the mail object.
     *
     * @access public
     * @param string $key The attribute key.
     * @return mixed A reference to the attribute, or null if no attribute value is set for the key.
     */
    public function & get($key) {
        $value= null;
        if (isset($this->attributes[$key])) {
            $value = & $this->attributes[$key];
        }
        return $value;
    }

    /**
     * Sets the value of an attribute of the mail object.
     *
     * {@internal Override this method in a derivative to set the appropriate attributes of the
     * actual mailer implementation being used. Make sure to call this parent implementation first
     * and then set the value of the corresponding mailer attribute as a reference to the attribute
     * set in $this->attributes}
     *
     * @abstract
     * @access public
     * @param string $key The key of the attribute to set.
     * @param mixed $value The value of the attribute.
     */
    public function set($key, $value) {
        $this->attributes[$key]= $value;
    }

    /**
     * Add a new recipient email address to one of the valid address type buckets.
     *
     * @access public
     * @param string $type The address type to add; to, cc, bcc, or reply-to.
     * @param string $email The email address.
     * @param string $name An optional name for the addressee.
     * @return boolean Indicates if the address was set/unset successfully.
     */
    public function address($type, $email, $name= '') {
        $set= false;
        $type = strtolower($type);
        if (isset($this->addresses[$type])) {
            if ($email === null && isset($this->addresses[$type][$email])) {
                $this->addresses[$type][$email]= null;
                $set= true;
            } else {
                $this->addresses[$type][$email]= array($email, $name);
                $set= true;
            }
        }
        return $set;
    }

    /**
     * Adds a header to the mailer
     *
     * @access public
     * @param string $header The HTTP header to send.
     * @return boolean True if the header is valid and is set.
     */
    public function header($header) {
        $set= false;
        $parsed= explode(':', $header, 2);
        if ($parsed && count($parsed) == 2) {
            $this->headers[]= $parsed;
            $set= true;
        }
        return $set;
    }

    /**
     * Send an email setting any supplied attributes before sending.
     *
     * {@internal You should implement the rest of this method in a derivative class.}
     *
     * @abstract
     * @access public
     * @param array $attributes Attributes to override any existing attributes before sending.
     * @return boolean Indicates if the email was sent successfully.
     */
    public function send(array $attributes= array()) {
        if (is_array($attributes)) {
            while (list($attrKey, $attrVal) = each($attributes)) {
                $this->set($attrKey, $attrVal);
            }
        }
        return false;
    }

    /**
     * Reset the mail service, clearing addresses and attributes.
     *
     * @access public
     * @param array $attributes An optional array of attributes to apply after reset.
     */
    public function reset(array $attributes = array()) {
        $this->addresses= array(
            'to' => array(),
            'cc' => array(),
            'bcc' => array(),
            'reply-to' => array(),
        );
        $attributes = $this->getDefaultAttributes($attributes);
        foreach ($this->attributes as $attrKey => $attrVal) {
            if (array_key_exists($attrKey, $attributes)) {
                $this->set($attrKey, $attributes[$attrKey]);
            } else {
                $this->set($attrKey, null);
            }
        }
    }

    /**
     * Get an instance of the email class responsible for sending emails from the modEmail service.
     *
     * {@internal Implement this function in derivatives and call it in the constructor after all
     * other dependencies have been satisfied.}
     *
     * @abstract
     * @access protected
     * @return boolean Indicates if the mailer class was instantiated successfully.
     */
    abstract protected function _getMailer();

    /**
     * Attach a file to the attachments queue.
     *
     * @access public
     * @param string $file The absolute path to the file
     */
    public function attach($file) {
        array_push($this->files,$file);
    }

    /**
     * Clear all existing attachments.
     *
     * @access public
     */
    public function clearAttachments() {
        $this->files = array();
    }
}