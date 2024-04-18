<?php

use PHPUnit\Framework\TestCase;
require __DIR__ . "/../src/Model/UserModel.php";

class UserModelTest extends TestCase
{
    private $userModel;
    private $db;

     protected function setUp(): void {
        $this->db = $this->createMock(PDO::class);
        $this->userModel = new UserModel($this->db);
     }

     public function testDoesEmailExist() {
          $stmt = $this->createMock(PDOStatement::class);
          $stmt->method('fetch')->willReturn(true);
          $this->db->method('prepare')->willReturn($stmt);

          $result = $this->userModel->doesEmailExist('example@example.com');
          $this->assertTrue($result);
     }

    public function testRegisterUser()
    {
        $db = new PDO('sqlite::memory:');
        $db->exec("CREATE TABLE utilisateurs (id INTEGER PRIMARY KEY, nom TEXT, prenom TEXT, adresse TEXT, email TEXT)");
        $db->exec("INSERT INTO utilisateurs (nom, prenom, adresse, email) VALUES ('John', 'Doe', '123 Any Street', 'john@example.com')");

        $model = new UserModel($db);
        $userEmailExists = $model->doesEmailExist('john@example.com');

        $this->assertTrue($userEmailExists);
        
    }

    public function testGetUserByEmail() {
        $email = 'user@example.com';
        $expectedResult = ['id' => 1, 'email' => $email, 'password' => 'hash'];
    
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$email]);
        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn($expectedResult);
    
        $this->db->expects($this->once())
             ->method('prepare')
             ->with("SELECT id, email, password FROM utilisateurs WHERE email = ?")
             ->willReturn($stmt);
    
        $result = $this->userModel->getUserByEmail($email);
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testGetUserById() {
        $id = 1;
        $expectedResult = ['id' => $id, 'nom' => 'John', 'prenom' => 'Doe', 'adresse' => '123 Main St', 'email' => 'john@example.com'];
    
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$id]);
        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn($expectedResult);
    
        $this->db->expects($this->once())
             ->method('prepare')
             ->with("SELECT id, nom, prenom, adresse, email FROM utilisateurs WHERE id = ?")
             ->willReturn($stmt);
    
        $result = $this->userModel->getUserById($id);
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testUpdateUser() {
        $id = 1;
        $nom = 'Jane';
        $prenom = 'Doe';
        $adresse = '124 Main St';
        $email = 'jane.doe@example.com';
        $hashedPassword = 'newhash';
    
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$nom, $prenom, $adresse, $email, $hashedPassword, $id])
             ->willReturn(true);
    
        $this->db->expects($this->once())
             ->method('prepare')
             ->with("UPDATE utilisateurs SET nom = ?, prenom = ?, adresse = ?, email = ?, password = ? WHERE id = ?")
             ->willReturn($stmt);
    
        $this->userModel->updateUser($id, $nom, $prenom, $adresse, $email, $hashedPassword);
    }
    
    public function testDoesEmailExistForOtherUser() {
        $email = 'jane.doe@example.com';
        $id = 2;
        $expectedResult = true;
    
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$email, $id])
             ->willReturn(true);
        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn(['id' => 1]);
    
        $this->db->expects($this->once())
             ->method('prepare')
             ->with("SELECT id FROM utilisateurs WHERE email = ? AND id != ?")
             ->willReturn($stmt);
    
        $result = $this->userModel->doesEmailExistForOtherUser($email, $id);
        $this->assertEquals($expectedResult, $result);
    }
    
    public function testDeleteUser() {
        $id = 1;
    
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$id])
             ->willReturn(true);
    
        $this->db->expects($this->once())
             ->method('prepare')
             ->with("DELETE FROM utilisateurs WHERE id = ?")
             ->willReturn($stmt);
    
        $this->userModel->deleteUser($id);
    }
    
}