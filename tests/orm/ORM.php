<?php

namespace Gini\PHPUnit\ORM;

require_once __DIR__ . '/../gini.php';

class ORM extends \Gini\PHPUnit\CLI {

    public function setUp() {
        parent::setUp();
        
        class_exists('\Gini\Those');

        $db = $this->getMockBuilder('\Gini\Database')
             ->setMethods(['query', 'quote', 'quoteIdent'])
             ->disableOriginalConstructor()
             ->getMock();
 
        $db->expects($this->any())
            ->method('quoteIdent')
            ->will($this->returnCallback(function($s) use($db) {
                if (is_array($s)) {
                    foreach ($s as &$i) {
                        $i = $db->quoteIdent($i);
                    }
        
                    return implode(',', $s);
                }
        
                return '"'.addslashes($s).'"';
            }));
         
        $db->expects($this->any())
            ->method('quote')
            ->will($this->returnCallback(function($s) use($db) {
                if (is_array($s)) {
                    foreach ($s as &$i) {
                        $i = $db->quote($i);
                    }
        
                    return implode(',', $s);
                } elseif (is_null($s)) {
                    return 'NULL';
                } elseif (is_bool($s)) {
                    return $s ? 1 : 0;
                } elseif (is_int($s) || is_float($s)) {
                    return $s;
                }
        
                return '\''.addslashes($s).'\'';
            }));

        // Mocking \Gini\ORM\UT_Lab
        //         
        // class UTSample extends \Gini\ORM\Object {
        // 
        //     var $name        = 'string:50';
        //     var $gender      = 'bool';
        //     var $money       = 'double,default:0';
        //     var $description = 'string:*,null';
        // 
        // }

        \Gini\IoC::bind('\Gini\ORM\UTSample', function() use ($db) {
            
            $o = $this->getMockBuilder('\Gini\ORM\Object')
                 ->setMethods(['db','structure', 'name', 'tableName'])
                 ->disableOriginalConstructor()
                 ->getMock();

            $o->expects($this->any())
                ->method('db')
                ->will($this->returnValue($db));

            $o->expects($this->any())
                ->method('name')
                ->will($this->returnValue('utsample'));

            $o->expects($this->any())
                ->method('tableName')
                ->will($this->returnValue('utsample'));

            $structure = [
                'id' => [ 'bigint' => null, 'primary' => null, 'serial' => null ],
                '_extra' => [ 'array' => null ],
                'object' => [ 'object' => null],
            ];

            $o->expects($this->any())
                ->method('structure')
                ->will($this->returnValue($structure));
                                            
            return $o;
        });

    }

    public function tearDown() {
        \Gini\IoC::clear('\Gini\ORM\UTSample');
        parent::tearDown();
    }
    
    public function testSave() {
        
        $o1 = a('utsample');
        $o2 = a('utsample');
        
        $o2->id = 10;
        $o1->object = $o2;
        $o1->db()
            ->expects($this->any())
            ->method('query')
            ->will($this->returnCallback(function($SQL) {
                $this->assertEquals($SQL, 
                    'INSERT INTO "utsample" ("_extra","object_name","object_id") VALUES(\'{}\',\'utsample\',10)');
            }));
            
        $o1->save();
    }

}
