<?php namespace MONEI\MONEI\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Orders extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'monei.orders' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('MONEI.MONEI', 'main-menu-monei');
    }
}
