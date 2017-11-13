<?php

namespace App\Http\Controllers;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use App\Mappers\ItemCatalogMapper;
use App\Gateway;
use App\Gateway\DesktopGateway;
use App\Gateway\TabletGateway;
use App\Gateway\LaptopGateway;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Tablet;


class ComputerController extends Controller
{

    public function index() {

    }

    public function showDesktop() {


        return view('items.computer.show-desktop', ['desktops' => ItemCatalogMapper::getInstance()->selectAllItemType(3)]);
    }

    public function showLaptop() {

        return view('items.computer.show-laptop', ['laptops' => ItemCatalogMapper::getInstance()->selectAllItemType(4)]);
    }

    public function showTablet() {

        return view('items.computer.show-tablet', ['tablets' => ItemCatalogMapper::getInstance()->selectAllItemType(5)]);
    }

    public function insertDesktop()
    {
        if($this->isFormSubmitted($_POST)) {

            $sanitizedInputs = filter_input_array(INPUT_POST, $this->desktopValidationFormInputs());
            $emptyArrayKeys = array_keys($sanitizedInputs, "");
            if (!empty($emptyArrayKeys)) {
                return view('items.create', ['inputErrors' => $emptyArrayKeys, 'alertType' => 'warning']);
            } else {
                $desktopItem = [ // it is assumed all the values are good here, can now insert
                    "processor_type" => $sanitizedInputs["desktop-processor"],
                    "ram_size" => $sanitizedInputs["desktop-ram-size"],
                    "cpu_cores" => $sanitizedInputs["desktop-cpu-cores"],
                    "weight" => $sanitizedInputs["desktop-weight"],
                    "hdd_size" => $sanitizedInputs["storage-capacity"],
                    "category" => "desktop",
                    "brand" => $sanitizedInputs["computer-brand"],
                    "price" => $sanitizedInputs["desktop-price"],
                    "quantity" => $sanitizedInputs["desktop-qty"],
                    "height" => $sanitizedInputs["desktop-height"],
                    "width" => $sanitizedInputs["desktop-width"],
                    "thickness" => $sanitizedInputs["desktop-thickness"]
                ];
                $desktopGateway = new DesktopGateway();
                $desktopGateway->insert($desktopItem);
                return redirect()->back()->with(['succeedInsertingItem' => true, 'for' => 'desktop']);
            }
        } else {
            return view('items.create');
        }
    }

    public function insertLaptop()
    {
        if($this->isFormSubmitted($_POST)) {
            $sanitizedInputs = filter_input_array(INPUT_POST, $this->laptopValidationFormInputs());
            $emptyArrayKeys = array_keys($sanitizedInputs, "");
            if (!empty($emptyArrayKeys)) {
                return view('items.create', ['inputErrors' => $emptyArrayKeys, 'alertType' => 'warning']);
            } else {
                $laptopItem = [
                    "processor_type" => $sanitizedInputs['laptop-processor'],
                    "ram_size" => $sanitizedInputs['laptop-ram-size'],
                    "cpu_cores" => $sanitizedInputs['laptop-cpu-cores'],
                    "weight" => $sanitizedInputs['laptop-weight'],
                    "hdd_size" => $sanitizedInputs["laptop-storage-capacity"],
                    "category" => "laptop",
                    "brand" => $sanitizedInputs['laptop-brand'],
                    "price" => $sanitizedInputs['laptop-price'],
                    "quantity" => $sanitizedInputs['laptop-qty'],
                    "display_size" => $sanitizedInputs['laptop-display-size'],
                    "os" => $sanitizedInputs['laptop-os'],
                    "battery" => $sanitizedInputs['laptop-battery'],
                    "camera" => $sanitizedInputs['laptop-camera'],
                    "is_touchscreen" => $sanitizedInputs['laptop-touchscreen'],
                ];
                $laptopGateway = new LaptopGateway();
                $laptopGateway->insert($laptopItem);
                return redirect()->back()->with(['succeedInsertingItem' => true, 'for' => 'laptop']);
            }
        } else {
            return view('items.create');
        }
    }

    public function insertTablet()
    {

        if($this->isFormSubmitted($_POST)) {
            $sanitizedInputs = filter_input_array(INPUT_POST, $this->tabletValidationFormInputs());
            $emptyArrayKeys = array_keys($sanitizedInputs, "");
            if (!empty($emptyArrayKeys)) {
                return view('items.create', ['inputErrors' => $emptyArrayKeys, 'alertType' => 'warning']);
            } else {

                $params = [
                    "processorType" => $sanitizedInputs['tablet-processor'],
                    "ramSize" => $sanitizedInputs['tablet-ram-size'],
                    "cpuCores" => $sanitizedInputs['tablet-cpu-cores'],
                    "weight" => $sanitizedInputs['tablet-weight'],
                    "hddSize" => $sanitizedInputs["tablet-storage-capacity"],
                    "category" => "tablet",
                    "brand" => $sanitizedInputs['tablet-brand'],
                    "price" => $sanitizedInputs['tablet-price'],
                    "quantity" => $sanitizedInputs['tablet-qty'],
                    "displaySize" => $sanitizedInputs['tablet-display-size'],
                    "width" => $sanitizedInputs['tablet-width'],
                    "height" => $sanitizedInputs['tablet-height'],
                    "thickness" => $sanitizedInputs['tablet-thickness'],
                    "battery" => $sanitizedInputs['tablet-battery'],
                    "os" => $sanitizedInputs['tablet-os'],
                    "camera" => $sanitizedInputs['tablet-camera'],
                    "isTouchscreen" => $sanitizedInputs['tablet-touchscreen']
                ];

                $addTabletItem = ItemCatalogMapper::getInstance();
                $addTabletItem->addNewItem($_SESSION['session_id'], 5, $params); // ufw
                $addTabletItem->commit($_SESSION['session_id']);
                return redirect()->back()->with(['succeedInsertingItem' => true, 'for' => 'tablet']);
            }
        } else {
            return view('items.create');
        }
    }

    public function deleteDesktop()
    {

    }

    public function deleteTablet() {
        if($this->isFormSubmitted($_POST)) {
            $itemId = filter_input(INPUT_POST, 'item-id', FILTER_SANITIZE_SPECIAL_CHARS);
            if(!empty($itemId)) {
                $itemMapper = ItemCatalogMapper::getInstance();
                $itemMapper->removeItem($_SESSION['session_id'], $itemId);
                $itemMapper->commit($_SESSION['session_id']);
            } else {
                return view('items.create');
            }
        }
        return view('items.create');
    }
}

