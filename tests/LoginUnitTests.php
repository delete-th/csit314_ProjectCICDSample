<?php
use PHPUnit\Framework\TestCase;
use Src\controllers\LoginController;

require_once __DIR__ . '/../src/controllers/LoginController.php';
require_once __DIR__ . '/../src/entities/UserAccount.php';
require_once __DIR__ . '/../src/entities/UserProfile.php';

class LoginControllerTest extends TestCase
{
    private $loginController;
    private $userAccountMock;
    private $userProfileMock;

    protected function setUp(): void
    {
        $this->userAccountMock = $this->createMock(UserAccount::class);
        $this->userProfileMock = $this->createMock(UserProfile::class);

        $this->loginController = new LoginController(
            $this->userAccountMock,
            $this->userProfileMock
        );
    }

    public function testNoAccountFound()
    {
        $this->userAccountMock->method('login')->willReturn([]);
        $this->userProfileMock->method('getProfileIDByName')->willReturn(['pID'=>1]);

        $result = $this->loginController->loginUser('alice','password','User Admin');
        $this->assertEquals(["This user does not have an account.",""], $result);
    }

    public function testProfileActive()
    {
        $this->userAccountMock->method('login')->willReturn(['profile'=>1,'aID'=>10]);
        $this->userProfileMock->method('getProfileIDByName')->willReturn(['pID'=>1]);
        $this->userProfileMock->method('getProfileByID')->willReturn(['status'=>'active','name'=>'User Admin']);

        $result = $this->loginController->loginUser('alice','password','User Admin');
        $this->assertEquals(['1_UserAdmin_Menu',10], $result);
    }
}
