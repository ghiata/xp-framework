<?php namespace net\xp_framework\unittest\rdbms;
 
use rdbms\DSN;
use rdbms\Criteria;
use rdbms\mysql\MySQLConnection;
use unittest\TestCase;
use rdbms\join\JoinProcessor;
use rdbms\join\JoinIterator;
use net\xp_framework\unittest\rdbms\dataset\Job;
use net\xp_framework\unittest\rdbms\mock\MockResultSet;

/**
 * Test JoinProcessor class
 *
 * Note: We're relying on the connection to be a mysql connection -
 * otherwise, quoting and date representation may change and make
 * this testcase fail.
 *
 * @see    xp://rdbms.join.JoinIterator
 */
class JoinIteratorTest extends TestCase {
  
  /**
   * Setup test
   */
  #[@beforeClass]
  public static function registerConnection() {
    \rdbms\ConnectionManager::getInstance()->register(new MySQLConnection(new DSN('mysql://localhost:3306/')), 'jobs');
  }
  
  #[@test, @expect('util.NoSuchElementException')]
  public function emptyResultNextTest() {
    create(new JoinIterator(new JoinProcessor(Job::getPeer()), new MockResultSet()))->next();
  }
  
  #[@test]
  public function emptyResultHasNextTest() {
    $this->assertFalse(create(new JoinIterator(new JoinProcessor(Job::getPeer()), new MockResultSet()))->hasNext());
  }
  
  #[@test]
  public function resultHasNextTest() {
    $rs= new MockResultSet(
      array(
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          't1_person_id'     => '11',
          't1_name'          => 'Schultz',
          't1_job_id'        => '21',
          't1_department_id' => '31',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          't1_person_id'     => '12',
          't1_name'          => 'Friebe',
          't1_job_id'        => '11',
          't1_department_id' => '31',
        ),
      )
    );
    $ji= new JoinIterator(new JoinProcessor(Job::getPeer()), $rs);
    $this->assertTrue($ji->hasNext());
    $this->assertClass($ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertFalse($ji->hasNext());
  }

  #[@test]
  public function multipleResultTest() {
    $rs= new MockResultSet(
      array(
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '12',
          JoinProcessor::FIRST.'_title'         => 'second job',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '13',
          JoinProcessor::FIRST.'_title'         => 'third job',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
        ),
      )
    );
    $jp= new JoinProcessor(Job::getPeer());
    $ji= new JoinIterator($jp, $rs);
    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertFalse($ji->hasNext());
  }

  #[@test]
  public function multipleJoinResultTest() {
    $rs= new MockResultSet(
      array(
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '11',
          JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'Schultz',
          JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '21',
          JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '11',
          JoinProcessor::FIRST.'_title'         => 'clean toilette',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '12',
          JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'M�ller',
          JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '11',
          JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '12',
          JoinProcessor::FIRST.'_title'         => 'second job',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => '11',
          JoinProcessor::pathToKey(array('PersonJob')).'_name'          => 'Schultz',
          JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => '21',
          JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => '31',
        ),
        array(
          JoinProcessor::FIRST.'_job_id'        => '13',
          JoinProcessor::FIRST.'_title'         => 'third job',
          JoinProcessor::FIRST.'_valid_from'    => new \util\Date(),
          JoinProcessor::FIRST.'_expire_at'     => '',
          JoinProcessor::pathToKey(array('PersonJob')).'_person_id'     => null,
          JoinProcessor::pathToKey(array('PersonJob')).'_name'          => null,
          JoinProcessor::pathToKey(array('PersonJob')).'_job_id'        => null,
          JoinProcessor::pathToKey(array('PersonJob')).'_department_id' => null,
        ),
      )
    );
    $jp= new JoinProcessor(Job::getPeer());
    $jp->setFetchModes(array('PersonJob' => 'join'));
    $ji= new JoinIterator($jp, $rs);

    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertArray($job->getPersonJobList());
    $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');

    $this->assertTrue($pji->hasNext());
    $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
    $this->assertTrue($pji->hasNext());
    $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
    $this->assertFalse($pji->hasNext());

    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertArray($job->getPersonJobList());
    $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');
    $this->assertTrue($pji->hasNext());
    $this->assertClass($pji->next(), 'net.xp_framework.unittest.rdbms.dataset.Person');
    $this->assertFalse($pji->hasNext());

    $this->assertTrue($ji->hasNext());
    $this->assertClass($job= $ji->next(), 'net.xp_framework.unittest.rdbms.dataset.Job');
    $this->assertArray($job->getPersonJobList());
    $this->assertClass($pji= $job->getPersonJobIterator(), 'util.HashmapIterator');
    $this->assertFalse($pji->hasNext());

    $this->assertFalse($ji->hasNext());
  }
}
