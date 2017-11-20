<?php

namespace App\Http\Controllers;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Gateway\MonitorGateway;
use App\Gateway\TabletGateway;
use App\Gateway\LaptopGateway;
use App\Gateway\DesktopGateway;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const MONITOR_ITEM_TYPE = 1;
    const DESKTOP_ITEM_TYPE = 3;
    const LAPTOP_ITEM_TYPE = 4;
    const TABLET_ITEM_TYPE = 5;

    // utility
    public $filterInputFloatArr = [
        'filter' => FILTER_VALIDATE_FLOAT,
        'options' => "decimal"
    ];

    // utility
    public $filterIntInputQty = [
        'filter' => FILTER_VALIDATE_INT,
        'options' => ['min_range' => 1, 'max_range' => 999]
    ];

    /**
     * Used for validating desktop form
     * in PHP, adding and modifying function
     * can use this function
     * @return array
     */
    public function desktopValidationFormInputs()
    {
        return [
            'desktop-brand' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'desktop-processor' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'desktop-ram-size' => FILTER_VALIDATE_INT,
            'desktop-storage-capacity' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'desktop-cpu-cores' => FILTER_VALIDATE_INT,
            'desktop-price' => $this->filterInputFloatArr,
            'desktop-weight' => $this->filterInputFloatArr,
            'desktop-height' => $this->filterInputFloatArr,
            'desktop-width' => $this->filterInputFloatArr,
            'desktop-thickness' => $this->filterInputFloatArr
        ];
    }

    /**
     * Used for validating laptop form
     * in PHP, adding and modifying function
     * can use this function
     * @return array
     */
    public function laptopValidationFormInputs()
    {
        return [
            'laptop-brand' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'laptop-processor' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'laptop-ram-size' => FILTER_VALIDATE_INT,
            'laptop-storage-capacity' => FILTER_VALIDATE_INT,
            'laptop-cpu-cores' => FILTER_VALIDATE_INT,
            'laptop-os' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'laptop-display-size' => $this->filterInputFloatArr,
            'laptop-price' => $this->filterInputFloatArr,
            'laptop-weight' => $this->filterInputFloatArr,
            'laptop-battery' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'laptop-camera' => FILTER_SANITIZE_SPECIAL_CHARS,
            'laptop-touchscreen' => FILTER_SANITIZE_SPECIAL_CHARS
        ];
    }

    /**
     * Used for validating tablet form
     * in PHP, adding and modifying function
     * can use this function
     * @return array
     */
    public function tabletValidationFormInputs()
    {
        return $params = [
            'tablet-brand' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'tablet-processor' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'tablet-ram-size' => FILTER_VALIDATE_INT,
            'tablet-cpu-cores' => FILTER_VALIDATE_INT,
            'tablet-os' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'tablet-storage-capacity' => FILTER_VALIDATE_INT,
            'tablet-display-size' => $this->filterInputFloatArr,
            'tablet-price' => $this->filterInputFloatArr,
            'tablet-weight' => $this->filterInputFloatArr,
            'tablet-height' => $this->filterInputFloatArr,
            'tablet-width' => $this->filterInputFloatArr,
            'tablet-thickness' => $this->filterInputFloatArr,
            'tablet-battery' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'tablet-camera' => FILTER_SANITIZE_SPECIAL_CHARS,
            'tablet-touchscreen' => FILTER_SANITIZE_SPECIAL_CHARS
        ];
    }

    public function registerValidateFormInputs()
    {
        $args = [
            'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'last_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'email' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'phone_number' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'door_number' => FILTER_VALIDATE_INT,
            'street' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'city' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'province' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'country' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'postal_code' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        ];
        return $args;
    }

    /**
     * Used for validating monitor form
     * in PHP, adding and modifying function
     * can use this function
     * @return array
     */
    public function monitorValidationFormInputs()
    {
        return [
            'monitor-brand' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'monitor-price' => $this->filterInputFloatArr,
            'monitor-display-size' => $this->filterInputFloatArr,
            'monitor-weight' => $this->filterInputFloatArr
        ];
    }

    public function isFormSubmitted($method)
    {
        if ($method == $_POST) {
            return ($_SERVER['REQUEST_METHOD'] == 'POST');
        } else {
            return ($_SERVER['REQUEST_METHOD'] == 'GET');
        }
    }

    public function desktopSearchParams() {
        return [
            'brand' => filter_input(INPUT_GET, 'desktop-brand'),
            'storage' => filter_input(INPUT_GET, 'desktop-storage-capacity'),
            'ramSize' => filter_input(INPUT_GET, 'desktop-ram-size'),
            'maxPrice' => filter_input(INPUT_GET, 'max-price'),
            'minPrice' => filter_input(INPUT_GET, 'min-price')
        ];
    }

    public function laptopSearchParams() {
        return  [
            'brand' => filter_input(INPUT_GET, 'laptop-brand'),
            'storage' => filter_input(INPUT_GET, 'laptop-storage-capacity'),
            'ramSize' => filter_input(INPUT_GET, 'laptop-ram-size'),
            'maxPrice' => filter_input(INPUT_GET, 'max-price'),
            'minPrice' => filter_input(INPUT_GET, 'min-price')
        ];
    }

    public function tabletSearchParams() {
        return [
            'brand' => filter_input(INPUT_GET, 'tablet-brand'),
            'storage' => filter_input(INPUT_GET, 'tablet-storage-capacity'),
            'ramSize' => filter_input(INPUT_GET, 'tablet-ram-size'),
            'maxPrice' => filter_input(INPUT_GET, 'max-price'),
            'minPrice' => filter_input(INPUT_GET, 'min-price')
        ];
    }

    public function isAdminSearching() {
        $cond = false;
        $forms = [
            'admin-search-desktop-form',
            'admin-search-laptop-form',
            'admin-search-tablet-form',
            'admin-search-monitor-form'
        ];
        foreach ($forms as $form) {
            if(isset($_GET[$form])) {
                $cond = true;
                break;
            }
        }
        return $cond;
    }

    public function isAdminLoggedIn()
    {
        return isset($_SESSION['isAdmin']) &&
            !empty($_SESSION['isAdmin']) &&
            $_SESSION['isAdmin'] == 1;
    }

    public function returnItemUnits($itemType)
    {
        $item = null;
        if($itemType == $this::MONITOR_ITEM_TYPE) {
            $item = new MonitorGateway();
        } else if($itemType == $this::DESKTOP_ITEM_TYPE) {
            $item = new DesktopGateway();
        } else if($itemType == $this::LAPTOP_ITEM_TYPE) {
            $item = new LaptopGateway();
        } else if($itemType == $this::TABLET_ITEM_TYPE) {
            $item = new TabletGateway();
        }
        $arr = $item->getByCondition([]);
        $unitsArr = [];
        for ($i = 0; $i < count($arr); $i++) {
            $units = $item->getSerialNumberByID($arr[$i]['item_id'], 'units');
            for ($j = 0; $j < count($units); $j++) {
                if($units[$j]['status'] != 'AVAILABLE') {
                    continue;
                }
                $serial = $units[$j]['serial'];
                $units[$j] = $arr[$i];
                $units[$j]['serial'] = $serial;
                /*
                 * returnItemUnits returns an array of results
                 * where keys are the same as units column name
                 * e.g. display_size
                 *
                 * but in ItemCatalogMapper, the keys are camelcase
                 * so to use the same pattern (camelcase)
                 * need to unset and use the same key naming
                 *
                 */
                if(array_key_exists('processor_type', $units[$j])) {
                    $units[$j]['processorType'] = $units[$j]['processor_type'];
                    unset($units[$j]['processor_type']);
                }
                if(array_key_exists('ram_size', $units[$j])) {
                    $units[$j]['ramSize'] = $units[$j]['ram_size'];
                    unset($units[$j]['ram_size']);
                }
                if(array_key_exists('cpu_cores', $units[$j])) {
                    $units[$j]['cpuCores'] = $units[$j]['cpu_cores'];
                    unset($units[$j]['cpu_cores']);
                }
                if(array_key_exists('hdd_size', $units[$j])) {
                    $units[$j]['hddSize'] = $units[$j]['hdd_size'];
                    unset($units[$j]['hdd_size']);
                }
                if(array_key_exists('display_size', $units[$j])) {
                    $units[$j]['displaySize'] = $units[$j]['display_size'];
                    unset($units[$j]['display_size']);
                }
                if(array_key_exists('is_touchscreen', $units[$j])) {
                    $units[$j]['isTouchscreen'] = $units[$j]['is_touchscreen'];
                    unset($units[$j]['is_touchscreen']);
                }
                array_push($unitsArr, $units[$j]);
            }
        }
        return $unitsArr;
    }


}

