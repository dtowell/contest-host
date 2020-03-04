<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    
    public static function setUpBeforeClass():void {
        // optionally connect to DB?
    }

    public static function tearDownAfterClass():void {
        // ... ??? 
    }

    public function setUp():void {
        // start a new CURL object for each test, to avoid any carry-over state
    }
    
    public function tearDown():void {
        curl_close($this->ch);
    }

    public function test_admin_login():void {
        $this->ch = curl_init();
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($this->ch,CURLOPT_COOKIEFILE,"");
        curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,FALSE);
        curl_setopt($this->ch,CURLOPT_URL,"http://localhost:8000/login.php");
        curl_setopt($this->ch,CURLOPT_HEADER,false);
        $result = curl_exec($this->ch);
        $this->assertTrue($result!==FALSE);
        $result_code = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $this->assertEquals(200,$result_code);

        curl_setopt($this->ch,CURLOPT_POST,true);
        curl_setopt($this->ch,CURLOPT_URL,"http://localhost:8000/login.php");
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,['user'=>'admin','password'=>'secret','login'=>true]);
        $result = curl_exec($this->ch);
        $this->assertTrue($result!==FALSE);
        $result_code = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $this->assertEquals(200,$result_code,$result);
        $this->assertContains("refresh",$result);

        curl_setopt($this->ch,CURLOPT_POST,false);
        curl_setopt($this->ch,CURLOPT_URL,"http://localhost:8000/view_problems.php");
        $result = curl_exec($this->ch);
        $this->assertTrue($result!==FALSE);
        $result_code = curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
        $this->assertEquals(200,$result_code);
        $this->assertContains("Judge Menu",$result);
    }
}
