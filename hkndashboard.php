<?php
/**
 * Dashboard Stats Widget Module for PrestaShop 8/9
 *
 * @author    Daniel Ionașcu
 * @copyright 2025 Daniel Ionașcu
 * @license   MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/src/Service/DashboardDataService.php';

class HknDashboard extends Module
{
    public function __construct()
    {
        $this->name = 'hkndashboard';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Daniel Ionașcu';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => '9.9.9',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Dashboard Stats Widget');
        $this->description = $this->l('Add custom statistics widget to dashboard.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('dashboardZoneTwo')) {
            return false;
        }

        Configuration::updateValue('DASHBOARD_STATS_TYPE', 'views');
        Configuration::updateValue('DASHBOARD_VIEWS_DAYS', 30);
        Configuration::updateValue('DASHBOARD_STOCK_THRESHOLD', 5);

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        Configuration::deleteByName('DASHBOARD_STATS_TYPE');
        Configuration::deleteByName('DASHBOARD_VIEWS_DAYS');
        Configuration::deleteByName('DASHBOARD_STOCK_THRESHOLD');

        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $statsType = Tools::getValue('DASHBOARD_STATS_TYPE');
            $viewsDays = (int)Tools::getValue('DASHBOARD_VIEWS_DAYS');
            $stockThreshold = (int)Tools::getValue('DASHBOARD_STOCK_THRESHOLD');

            if ($viewsDays <= 0) {
                $output .= $this->displayError($this->l('View days must be greater than 0.'));
            } elseif ($stockThreshold < 0) {
                $output .= $this->displayError($this->l('Stock threshold cannot be negative.'));
            } else {
                Configuration::updateValue('DASHBOARD_STATS_TYPE', pSQL($statsType));
                Configuration::updateValue('DASHBOARD_VIEWS_DAYS', $viewsDays);
                Configuration::updateValue('DASHBOARD_STOCK_THRESHOLD', $stockThreshold);

                $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));
            }
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Dashboard Widget Settings'),
                    'icon' => 'icon-dashboard',
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->l('Widget Type'),
                        'name' => 'DASHBOARD_STATS_TYPE',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'views',
                                    'name' => $this->l('Top Viewed Products'),
                                ],
                                [
                                    'id' => 'stock',
                                    'name' => $this->l('Low Stock Alerts'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('View Days'),
                        'name' => 'DASHBOARD_VIEWS_DAYS',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Number of days to look back for product views'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Low Stock Threshold'),
                        'name' => 'DASHBOARD_STOCK_THRESHOLD',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Show products with stock below this number'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        return [
            'DASHBOARD_STATS_TYPE' => Configuration::get('DASHBOARD_STATS_TYPE'),
            'DASHBOARD_VIEWS_DAYS' => Configuration::get('DASHBOARD_VIEWS_DAYS'),
            'DASHBOARD_STOCK_THRESHOLD' => Configuration::get('DASHBOARD_STOCK_THRESHOLD'),
        ];
    }

    public function hookDashboardZoneTwo($params)
    {
        if (!$this->active) {
            return;
        }

        $statsType = Configuration::get('DASHBOARD_STATS_TYPE');
        $viewsDays = (int)Configuration::get('DASHBOARD_VIEWS_DAYS');
        $stockThreshold = (int)Configuration::get('DASHBOARD_STOCK_THRESHOLD');

        $service = new DashboardDataService();
        $data = [];
        $title = '';

        if ($statsType === 'views') {
            $data = $service->getMostViewedProducts($viewsDays, 5);
            $title = sprintf($this->l('Top 5 Viewed Products (Last %d Days)'), $viewsDays);
        } else {
            $data = $service->getLowStockAlerts($stockThreshold);
            $title = sprintf($this->l('Low Stock Alerts (Below %d)'), $stockThreshold);
        }

        $this->context->smarty->assign([
            'widget_title' => $title,
            'stats_type' => $statsType,
            'data' => $data,
            'admin_product_link' => $this->context->link->getAdminLink('AdminProducts'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/dashboard_widget.tpl');
    }
}
