<?php

namespace App\Gateway;

class UserGateway
{
    private $db;
    private $tableName;

    public function __construct() {
        $this->tableName = "users";
        $this->db = new DatabaseGateway();
    }

    private function getUserQuery($conditionsAssociativeArray) {
        $conditions = transformConditionsToString($conditionsAssociativeArray);
        $sql = "SELECT * FROM $this->tableName WHERE $conditions;";
        $result = $this->db->queryDB($sql);
        if ($result !== null) {
            return parseSelectResult($result);
        } else {
            return null;
        }
    }

    public function getUserByEmail($email) {
        $conditionsAssociativeArray = ["email" => $email];
        return $this->getUserQuery($conditionsAssociativeArray);
    }

    public function getUserById($id) {
        $conditionsAssociativeArray = ["id" => $id];
        return $this->getUserQuery($conditionsAssociativeArray);
    }

    public function editUser($id, $email, $password, $firstName, $lastName, $phoneNumber,
    $doorNumber, $appartement, $street, $city, $province, $country, $postalCode) {
        $columnValuePairsAssociativeArray = [
            "email" => $email,
            "password" => $password,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "phone_number" => $phoneNumber,
            "door_number" => $doorNumber,
            "appartement" => $appartement,
            "street" => $street,
            "city" => $city,
            "province" => $province,
            "country" => $country,
            "postal_code" => $postalCode,
        ];
        $conditionsAssociativeArray = ["id" => $id];
        return $this->db->update($columnValuePairsAssociativeArray, $conditionsAssociativeArray);
    }

    public function addUser($email, $password, $firstName, $lastName, $phoneNumber,
    $doorNumber, $appartement, $street, $city, $province, $country, $postalCode, $isAdmin) {
        $columnValueAssociativeArray = [
            "email" => $email,
            "password" => $password,
            "first_name" => $firstName,
            "last_name" => $lastName,
            "phone_number" => $phoneNumber,
            "door_number" => $doorNumber,
            "appartement" => $appartement,
            "street" => $street,
            "city" => $city,
            "province" => $province,
            "country" => $country,
            "postal_code" => $postalCode,
            "isAdmin" => $isAdmin
        ];
        return $this->db->insert($columnValueAssociativeArray);
    }

    public function deleteUserByEmail($email) {
        $conditionsAssociativeArray = ["email" => $email];
        return $this->db->delete($conditionsAssociativeArray);
    }

    public function deleteUserById($id) {
        $conditionsAssociativeArray = ["id" => $id];
        return $this->db->delete($conditionsAssociativeArray);
    }
}