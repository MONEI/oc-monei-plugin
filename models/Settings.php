<?php namespace MONEI\MONEI\Models;

use Model;
use System\Models\File;
use Cms\Classes\Page;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'monei_monei_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'logo' => File::class,
    ];
    
    public function initSettingsData()
    {
        $this->mode = 0;
        $this->paypal_email = '';
        $this->currency_code = '';
        $this->return_page = '';
    }

    public function configArray()
    {
        $out = [];
        $arFields = $this->getFieldConfig()->fields;
        $arFields = array_keys(array_merge($arFields, $this->getFieldConfig()->tabs['fields']));

        foreach ($arFields as $sField) {
            $out[$sField] = self::get($sField);
        }

        return $out;
    }

    public function getUrlCancelOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getUrlCompleteOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
}
