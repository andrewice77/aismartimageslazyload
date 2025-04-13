<?php
/**
 * Copyright 2024 AndrewIce77
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to me@andreascipio.it so we can send you a copy immediately.
 *
 * @author    Andrea Scipio <me@andreascipio.it>
 * @copyright 2024 AndrewIce77
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_'))
    exit;


class aismartimageslazyload extends Module
{

    protected array $configuration;

    public function __construct($name = null, Context $context = null)
    {
        $this->name = 'aismartimageslazyload';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'AndrewIcE';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct($name, $context);

        $this->displayName = $this->trans('Smart Images Lazy Load', array(), 'Modules.ImagesLazyLoad.Admin');
        $this->description = $this->trans('Smart Images Lazy Load', array(), 'Modules.ImagesLazyLoad.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', array(), 'Modules.ImagesLazyLoad.Admin');

        $this->ps_versions_compliancy = array('min' => '8.0.0', 'max' => _PS_VERSION_);
    }


    /**
     * Install module and hooks.
     * @return bool
     */
    public function install()
    {
        return  parent::install() &&
                $this->installHook();
    }

    /**
     * Uninstall module
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->configuration = $this->initParams();

        $output = '';

        if( \Tools::isSubmit('submit' . $this->name) && $this->saveParams() )
            $output .= $this->displayConfirmation($this->l('Settings updated'));

        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Smart Lazy Load Settings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Enable Lazy Load'),
                    'name' => 'SMARTIMAGESLAZYLOAD',
                    'values' => $this->getSwitchValues(),
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Upload Placeholder Image'),
                    'name' => 'SMARTIMAGESLAZYLOAD_PLACEHOLDER',
                    'desc' => $this->l('Upload an image to be used as the placeholder. Supported formats: .jpg, .png, .gif'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Exclude CSS Classes'),
                    'desc' => $this->l('Separate class names with commas (e.g., .slider,.no-lazy)'),
                    'name' => 'SMARTIMAGESLAZYLOAD_EXCLUDED',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->submit_action = 'submit' . $this->name;
        $helper->fields_value = $this->initParams();

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayHeader()
    {
        $this->configuration = $this->initParams();

        if( (bool)$this->configuration['SMARTIMAGESLAZYLOAD'] ){
            $context = Context::getContext();
            $context->controller->registerJavascript(
                'modules-' . $this->name,
                'modules/' . $this->name . '/views/assets/js/lazyload.js',
                ['position' => 'bottom', 'priority' => 150]
            );

            $context->controller->registerStylesheet(
                'modules-' . $this->name,
                'modules/' . $this->name . '/views/assets/css/lazyload.css',
                ['media' => 'all', 'priority' => 150]
            );

            Media::addJsDef([
                'aismartimageslazyload' => [
                    'placeholder' => $this->configuration['SMARTIMAGESLAZYLOAD_PLACEHOLDER'],
                    'excluded' => $this->configuration['SMARTIMAGESLAZYLOAD_EXCLUDED'],
                ]
            ]);
        }
    }

    /**
     * Initialize module parameters
     * @return array
     */
    private function initParams()
    {
        return [
            'SMARTIMAGESLAZYLOAD'               => Configuration::get('SMARTIMAGESLAZYLOAD'),
            'SMARTIMAGESLAZYLOAD_PLACEHOLDER'   => Configuration::get('SMARTIMAGESLAZYLOAD_PLACEHOLDER'),
            'SMARTIMAGESLAZYLOAD_EXCLUDED'      => Configuration::get('SMARTIMAGESLAZYLOAD_EXCLUDED'),
        ];
    }

    /**
     * Save module parameters
     * @return true
     */
    private function saveParams()
    {
        $this->configuration['SMARTIMAGESLAZYLOAD']                 = \Tools::getValue('SMARTIMAGESLAZYLOAD');
        $this->configuration['SMARTIMAGESLAZYLOAD_EXCLUDED']        = \Tools::getValue('SMARTIMAGESLAZYLOAD_EXCLUDED');

        Configuration::updateValue('SMARTIMAGESLAZYLOAD', $this->configuration['SMARTIMAGESLAZYLOAD']);
        Configuration::updateValue('SMARTIMAGESLAZYLOAD_EXCLUDED', $this->configuration['SMARTIMAGESLAZYLOAD_EXCLUDED']);

        if (isset($_FILES['SMARTIMAGESLAZYLOAD_PLACEHOLDER']) && $_FILES['SMARTIMAGESLAZYLOAD_PLACEHOLDER']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = _PS_IMG_DIR_ . $this->name . DIRECTORY_SEPARATOR;
            $file_name = 'placeholder_' . time() . '.jpg';
            $file_path = $upload_dir . $file_name;

            // Assicurati che la cartella di destinazione esista
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }

            if (move_uploaded_file($_FILES['SMARTIMAGESLAZYLOAD_PLACEHOLDER']['tmp_name'], $file_path)) {
                Configuration::updateValue('SMARTIMAGESLAZYLOAD_PLACEHOLDER', $file_path);
            } else {
                $this->_errors[] = $this->l('An error occurred while uploading the image.');
            }
        }

        return true;
    }

    private function getSwitchValues()
    {
        return [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            ]
        ];
    }

    /**
     * Register hook module
     * @return bool
     */
    private function installHook()
    {
        return $this->registerHook('displayHeader');
    }
}