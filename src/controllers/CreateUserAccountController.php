<?php
// controllers/CreateUserAccountController.php
require_once __DIR__ . '/../entities/UserAccount.php';

class CreateUserAccountController {
    private $userAccountEntity;

    public function __construct($userAccount = null) {
        // Controller creates its own entity
        $this->userAccountEntity = $userAccount ?? new UserAccount();
    }

    public function CreateUserAccount($name, $password, $phoneNumber, $address, $dob, $userProfile, $status) {
        // Call the entity function to create a new user account
        $result = $this->userAccountEntity->createUserAccount($name, $password, $phoneNumber, $address, $dob, $userProfile, $status);
		
		return $result;

    }
}
?>

