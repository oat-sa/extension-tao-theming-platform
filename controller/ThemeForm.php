<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

namespace oat\taoThemingPlatform\controller;

use oat\taoThemingPlatform\model\PlatformThemingConfig;

/**
 * Allow you to modify the theme of the platform
 *
 * @access public
 * @package taoThemingPlatform
 
 */
class ThemeForm
    extends \tao_helpers_form_FormContainer
{

    public function __construct($options = array())
    {

        parent::__construct(array(), $options);

    }

    public function initForm()
    {

        $name = isset($this->options['name']) ? $this->options['name'] : 'form_'.(count(self::$forms)+1);
        unset($this->options['name']);

        $this->form = \tao_helpers_form_FormFactory::getForm($name, $this->options);

        $this->form->setActions(array(), 'top');
        $this->form->setActions(array(), 'bottom');

    }

    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {

        $themingConfig = isset($this->options['themingConfig'])?$this->options['themingConfig']:new PlatformThemingConfig();


        //Logo file input
        $fileElt = \tao_helpers_form_FormFactory::getElement('logo', 'AsyncFile');
        $fileElt->setDescription(__('Logo'));
        $this->form->addElement($fileElt);

        //Link input
        $textElt = \tao_helpers_form_FormFactory::getElement("link", 'Textbox');
        $textElt->setDescription(__('Link on logo click'));
        $textElt->setValue($themingConfig->offsetGet('link'));
        $this->form->addElement($textElt);

        //Message input
        $textElt = \tao_helpers_form_FormFactory::getElement("message", 'Textbox');
        $textElt->setDescription(__('Alternative text of the logo'));
        $textElt->setValue($themingConfig->offsetGet('message'));
        $this->form->addElement($textElt);

    }

}