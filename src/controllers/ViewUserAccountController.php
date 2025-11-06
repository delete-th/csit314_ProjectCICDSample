<?php
// controllers/ViewUserAccountController.php
require_once __DIR__ . '/../entities/UserAccount.php';

class ViewUserAccountController {
    private $userAccountEntity;

    public function __construct($userAccount = null) {
        // Controller creates its own entity
        $this->userAccountEntity = $userAccount ?? new UserAccount();
    }

    public function GetUserAccounts() {
        $accounts = $this->userAccountEntity->viewUserAccounts();
        return $accounts;
    }

    public function GetUserAccountByID($aID) {
        return $this->userAccountEntity->getUserAccountByID($aID);
    }

}
?>


