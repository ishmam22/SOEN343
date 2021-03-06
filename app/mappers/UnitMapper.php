<?php

namespace App\Mappers;

use App\Models\Unit;
use App\Gateway\UnitGateway;
use App\UnitOfWork\CollectionMapper;
use App\IdentityMap\IdentityMap;
use PhpDeal\Annotation as Contract;

// enum for the three possible unit statuses.
class UnitStatusEnum {
    const AVAILABLE = "AVAILABLE";
    const RESERVED = 'RESERVED';
    const PURCHASED = 'PURCHASED';
}

// prefixes the serial string to ensure it is unique when
// given to the identitiy map.
function mapSerial($serial) {
    return "unit-$serial";
}

// creates an sql timestamp of the current time.
function getDate() {
    return date("Y-m-d H:i:s");
}

// maintains a list of the available units and references
// to the in-memory objects. Although the identity map is
// used as the source of truth, this class is useful to
// represent the aggregate of units.
class UnitCatalog {
    private static $instance;

    private $catalog;

    private function __construct() {
        $this->catalog = array();
    }

    public static function getInstance(): UnitCatalog {
        if (self::$instance === null) {
            self::$instance = new UnitCatalog();
        }
        return self::$instance;
    }

    // helper to convert an array of column values into
    // a unit object.
    public function toObject(array $record): Unit {
        return new Unit(
            $record["serial"],
            $record["item_id"],
            $record["status"],
            $record["account_id"],
            $record["reserved_date"],
            $record["purchased_price"],
            $record["purchased_date"]
        );
    }

    // helper to convert an object of type unit into an
    // associative array.
    public function toArray(Unit $object): array {
        return array(
            "serial" => $object->getSerial(),
            "item_id" => $object->getItemId(),
            "status" => $object->getStatus(),
            "account_id" => $object->getAccountId(),
            "reserved_date" => $object->getReservedDate(),
            "purchased_price" => $object->getPurchasedPrice(),
            "purchased_date" => $object->getPurchasedDate()
        );
    }

    public function add(Unit $unit): bool {
        if (isset($this->catalog[$unit->getSerial()])) {
            return false;
        }
        $this->catalog[$unit->getSerial()] = $unit;
        return true;
    }

    public function remove(Unit $unit){
        unset($this->catalog[$unit->getSerial()]);
    }

    public function query($accountId, $status): array {
        $arr = array();
        foreach($this->catalog as $unit) {
            $isStatus = $unit->getStatus() === $status;
            $isAccount = $unit->getAccountId() === $accountId;
            if ($isStatus && $isAccount) {
                array_push($arr, $this->toArray($unit));
            }
        }
        return $arr;
    }

    public function fetchAvailableUnitsByItemId($itemId, $status): array {
        $arr = [];
        foreach ($this->catalog as $unit) {
            $isStatus = $unit->getStatus() === $status;
            $isItemId = $unit->getItemId() === $itemId;
            if($isStatus && $isItemId) {
                array_push($arr, $this->toArray($unit));
            }
        }
        return $arr;
    }

    public function reserve(Unit $unit, $accountId){
        $unit->setStatus(UnitStatusEnum::RESERVED);
        $unit->setAccountId($accountId);
        $unit->setReservedDate(getDate());
        $unit->setPurchasedPrice("NULL");
        $unit->setPurchasedDate("NULL");
    }

    public function checkout(Unit $unit, $accountId, $purchasedPrice){
        $unit->setStatus(UnitStatusEnum::PURCHASED);
        $unit->setAccountId($accountId);
        $unit->setReservedDate("NULL");
        $unit->setPurchasedPrice($purchasedPrice);
        $unit->setPurchasedDate(getDate());
    }

    public function return(Unit $unit){
        $unit->setStatus(UnitStatusEnum::AVAILABLE);
        $unit->setAccountId('NULL');
        $unit->setReservedDate("NULL");
        $unit->setPurchasedPrice("NULL");
        $unit->setPurchasedDate("NULL");
    }
}
/**
 * @Contract\Invariant("count($this->catalog) >= 0")
 */
class UnitMapper implements CollectionMapper {
    private static $instance;

    private $deletedUnit;
    private $unitGateway;
    private $identityMap;
    private $catalog;
    private $maxReservedMinutes;

    private function __construct() {
        $this->deletedUnit = new Unit(null, null, null, null, null, null, null);
        $this->unitGateway = UnitGateway::getInstance();
        $this->identityMap = IdentityMap::getInstance();
        $this->catalog = UnitCatalog::getInstance();

        // loading all units into the identity map/catalog.
        // since this is the executed in the constructor, it
        // is assumed that the identity map has no values for
        // this mapper and that nothing will be overwritten.
        $res = $this->unitGateway->select(array());
        if (!$res) {
            return;
        }
        for ($i = 0; $i < count($res); $i++) {
            $unit = $this->catalog->toObject($res[$i]);
            $this->identityMap->set(mapSerial($unit->getSerial()), $unit);
            $this->catalog->add($unit);
            // all accounts' reserved items are made available
            // if the reservation expires.
            if ($unit->getStatus() === UnitStatusEnum::RESERVED) {
                $this->maxReservedMinutes = 5;
                $secondsSinceReserved = time() - strtotime($unit->getReservedDate());
                if ($secondsSinceReserved > $this->maxReservedMinutes*60) {
                    $this->catalog->return($unit);
                    $this->edit($unit);
                }
            }
        }
    }

    public static function getInstance(): UnitMapper {
        if (self::$instance === null) {
            self::$instance = new UnitMapper();
        }
        return self::$instance;
    }

    // fetches unit object from identity map or from the gateway.
    // returns null if the unit is not found.
    private function getObject($serial): ?Unit {
        $exists = $this->identityMap->hasId(mapSerial($serial));
        if ($exists) {
            $unit = $this->identityMap->getObject(mapSerial($serial));
            // deleted units are set to this value so that reads
            // do not go fetch the value from the database (where
            // it still exists until work is committed)
            if ($unit === $this->deletedUnit) {
                return null;
            }
            return $unit;
        }
        $res = $this->unitGateway->select(["serial" => $serial]);
        if ($res === null) {
            return null;
        }
        $unit = $this->catalog->toObject($res[0]);
        $this->identityMap->set(mapSerial($serial), $unit);
        return $unit;
    }

    ////////////////////////////////
    ///  UNIT OF WORK INTERFACE  ///
    ////////////////////////////////

    public function add($object) {
        $this->unitGateway->insert(
            $object->getSerial(),
            $object->getItemId()
        );
        $this->edit($object);
    }

    public function edit($object) {
        $this->unitGateway->update(
            $object->getSerial(),
            $object->getItemId(),
            $object->getStatus(),
            $object->getAccountId(),
            $object->getReservedDate(),
            $object->getPurchasedPrice(),
            $object->getPurchasedDate()
        );
    }

    public function delete($object) {
        $this->unitGateway->delete($object->getSerial());
    }

    ////////////////////////////
    ///  CONTROLLER METHODS  ///
    ////////////////////////////

    public function commit($transactionId) {

        // INTERCEPTED IN AOP!!!
        // $this->unitOfWork->commit($transactionId);
    }

    // fetches an item from memory, and returns it as an
    // associative array.
    public function get($serial): ?array {
        $unit = $this->getObject($serial);
        if (!$unit) {
            return null;
        }
        return $this->catalog->toArray($unit);
    }

    // create a new unit. note that the status is not set in
    // this method. the units' actions are defined below.
    public function create($transactionId, $serial, $itemId): bool {
        // this can be done since the primary key (serial) is
        // is not auto-generated which means all the necessary
        // information is available.
        $unit = new Unit($serial, $itemId, UnitStatusEnum::AVAILABLE, "NULL", "NULL", "NULL", "NULL");
        $serial = $unit->getSerial();
        // the catalog returns whether the unit's id wasn't
        // already in the catalog, making the creation invalid.
        $success = $this->catalog->add($unit);
        if (!$success) {
            return false;
        }
        $this->identityMap->set(mapSerial($serial), $unit);
        $this->registerNew($transactionId, self::$instance, $unit);
        return true;
    }

    // delete unit from database.
    public function remove($transactionId, $serial) {
        $unit = $this->getObject($serial);
        if (!$unit) {
            return;
        }
        $this->catalog->remove($unit);
        $this->identityMap->set(mapSerial($serial), $this->deletedUnit);
        $this->registerDeleted($transactionId, mapSerial($serial), self::$instance, $unit);
    }

    /**
     * reserved units are associated with an account and
     * store their reserved time.
     * This is the contract for adding to cart
     *
     * @param integer $transactionId
     * @param string $serial
     * @param integer $accountId
     *
     * @Contract\Verify("!empty($transactionId) && !empty($serial) && !empty($accountId) && is_numeric($transactionId) && is_numeric($accountId)")
     * @Contract\Ensure("count($this->getCart($accountId) > $__old->count($this->getCart($accountId)")
     *
     * @return boolean
     */
    public function reserve($transactionId, $serial, $accountId): bool {
        $unit = $this->getObject($serial);
        if (!$unit) {
            return false;
        }
        $cartSize = count($this->getCart($accountId));
        if ($cartSize > 6) {
            return false;
        }
        $this->catalog->reserve($unit, $accountId);
        $this->registerDirty($transactionId, mapSerial($serial), self::$instance, $unit);
        return true;
    }

    /**
     * checked out units are associated with an account and
     * specify their purchase price and time.
     *
     * @param integer $transactionId
     * @param string $serial
     * @param integer $accountId
     * @param float $purchasedPrice
     *
     * This is the contract for purchases.
     * @Contract\Verify("!empty($transactionId) && !empty($serial) && !empty($accountId) && !empty($purchasedPrice) && $purchasedPrice > 0
     *                      && is_numeric($transactionId) && is_numeric($accountId)")
     * @Contract\Ensure("count($this->getCart($accountId) < $__old->count($this->getCart($accountId) && count($this->getCart($accountId) == 0")
     *
     * @return boolean
     */
    public function checkout($transactionId, $serial, $accountId, $purchasedPrice): bool {
        $unit = $this->getObject($serial);
        if (!$unit) {
            return false;
        }
        $this->catalog->checkout($unit, $accountId, $purchasedPrice);
        $this->registerDirty($transactionId, mapSerial($serial), self::$instance, $unit);
        return true;
    }

    /**
     * returned units are not associated to any account and
     * have no reserved/purchased fields. note that this method
     * is used to return from both the reserved and the purchased
     * states.
     *
     * @param integer $transactionId
     * @param string $serial
     *
     * This is the contract for returning items.
     * @Contract\Verify("!empty($transactionId) && !empty($serial) && is_numeric($transactionId)")
     * @Contract\Ensure("count($this->catalog) == $__old->count($this->catalog)")
     *
     * @return boolean
     */
    public function return($transactionId, $serial): bool {
        $unit = $this->getObject($serial);
        if (!$unit) {
            return false;
        }
        $this->catalog->return($unit);
        $this->registerDirty($transactionId, mapSerial($serial), self::$instance, $unit);
        return true;
    }

    public function getCart($accountId): array {
        return $this->catalog->query($accountId, UnitStatusEnum::RESERVED);
    }

    public function getPurchased($accountId): array {
        return $this->catalog->query($accountId, UnitStatusEnum::PURCHASED);
    }


    public function getAvailableUnitsByItemId($itemId) {
        return $this->catalog->fetchAvailableUnitsByItemId($itemId, UnitStatusEnum::AVAILABLE);
    }

    public function registerDirty($transactionId, $objectId, CollectionMapper $mapper, $object){
        // AOP INTERCEPTION
    }

    public function registerNew($transactionId, CollectionMapper $mapper, $object) {
        // AOP INTERCEPTION
    }

    public function registerDeleted($transactionId, $objectId, CollectionMapper $mapper, $object){
        // AOP INTERCEPTION
    }
}