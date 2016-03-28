<?php
/**
 * VisualPHPUnit
 *
 * VisualPHPUnit is a visual front-end for PHPUnit.
 *
 * PHP Version 5.6<
 *
 * @author    Johannes Skov Frandsen <localgod@heaven.dk>
 * @copyright 2011-2015 VisualPHPUnit
 * @license   http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link      https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace Visualphpunit\Core;

use \PHPUnit_Framework_TestSuite;
use \PHPUnit_Framework_TestResult;
use \PHPUnit_Framework_ExpectationFailedException;
use \PHPUnit_Framework_SelfDescribing;
use \Exception;

/**
 * Visualphpunit parser
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 */
class Parser
{

    /**
     * Run the list of test files
     *
     * @param array $tests
     *
     * @return array<string,double|integer|array>
     */
    public function run($tests)
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $this->addBootstrap($tests);
        $suite->addTestFiles($tests);
        return $this->parseTestSuite($suite->run(new PHPUnit_Framework_TestResult()));
    }

    /**
     * Require bootstrap if present
     *
     * @param array $tests
     *
     * @return void
     */
    private function addBootstrap($tests)
    {
        foreach ($tests as $filename) {
            if (file_exists($filename)) {
                $info = pathinfo($filename);
                if (file_exists($info['dirname'] . DIRECTORY_SEPARATOR . 'bootstrap.php')) {
                    require_once $info['dirname'] . DIRECTORY_SEPARATOR . 'bootstrap.php';
                }
            }
        }
    }

    /**
     * Parse the test suite result
     *
     * @param \PHPUnit_Framework_TestResult $result
     * @return array<string,double|integer|array>
     */
    private function parseTestSuite($result)
    {
        $passed = 0;
        $error = 0;
        $failed = 0;
        $notImplemented = 0;
        $skipped = 0;
        
        $tests = [];
        foreach ($result->passed() as $key => $value) {
            $tests[] = $this->parseTest('passed', $key);
            $passed ++;
        }
        foreach ($result->failures() as $obj) {
            $tests[] = $this->parseTest('failed', $obj);
            $failed ++;
        }
        foreach ($result->skipped() as $obj) {
            $tests[] = $this->parseTest('skipped', $obj);
            $skipped ++;
        }
        foreach ($result->notImplemented() as $obj) {
            $tests[] = $this->parseTest('notImplemented', $obj);
            $notImplemented ++;
        }
        foreach ($result->errors() as $obj) {
            $tests[] = $this->parseTest('error', $obj);
            $error ++;
        }
        
        usort($tests, function ($a, $b) {
            return strnatcmp($a['class'], $b['class']);
        });
        
        $data = [
            'time' => $result->time(),
            'total' => count($tests),
            'passed' => $passed,
            'error' => $error,
            'failed' => $failed,
            'notImplemented' => $notImplemented,
            'skipped' => $skipped,
            'tests' => $tests
        ];
        return $data;
    }

    /**
     * Filter the trace to exclude vendor and VPU classes
     *
     * @param array $trace
     * @todo This needs more work
     * @return mixed[]
     */
    private function filterTrace($trace)
    {
        $newTrace = [];
        if (! empty($trace)) {
            foreach ($trace as $entity) {
                if (isset($entity['file'])) {
                    if (! preg_match('/.*vendor/', $entity['file'])) {
                        if (! preg_match('/.*src\/Core|Api/', $entity['file'])) {
                            if (! preg_match('/.*backend\/index.php/', $entity['file'])) {
                                $newTrace[] = $entity;
                            }
                        }
                    }
                }
            }
        }
        return $newTrace;
    }

    /**
     * Parse individual test
     *
     * @param string $status
     * @param string|object $test
     *
     * @return mixed[]
     */
    private function parseTest($status, $test)
    {
        if (is_object($test)) {
            return [
                'class' => $this->explodeTestName($test->getTestName())['class'],
                'name' => $this->explodeTestName($test->getTestName())['method'],
                'friendly-name' => $this->friendlyName($this->explodeTestName($test->getTestName())['method']),
                'status' => $status,
                'message' => $test->thrownException()->getMessage(),
                'expected' => $this->getComparison($test->thrownException())['expected'],
                'actual' => $this->getComparison($test->thrownException())['actual'],
                'trace' => $this->filterTrace($test->thrownException()
                    ->getTrace())
            ];
        } else {
            return [
                'class' => $this->explodeTestName($test)['class'],
                'name' => $this->explodeTestName($test)['method'],
                'friendly-name' => $this->friendlyName($this->explodeTestName($test)['method']),
                'status' => $status,
                'message' => '',
                'expected' => '',
                'actual' => ''
            ];
        }
    }

    /**
     * Convert camelCase to friendly name
     *
     * @param sreing $camelCaseString
     *
     * @return string
     */
    private function friendlyName($camelCaseString)
    {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        $a[0] = ucfirst($a[0]);
        return join($a, " ");
    }

    /**
     * Explode a testname into class and method components
     *
     * @param string $testName
     * @return mixed[]
     */
    private function explodeTestName($testName)
    {
        preg_match('/([a-zA-Z0-9]+)::([a-zA-Z0-9]+)$/', $testName, $matches);
        return [
            'class' => $matches[1],
            'method' => $matches[2]
        ];
    }

    /**
     * Get expected and actual if available
     *
     * @param Exception $e
     *
     * @return mixed[]
     */
    private function getComparison(Exception $e)
    {
        if ($e instanceof PHPUnit_Framework_SelfDescribing) {
            if ($e instanceof PHPUnit_Framework_ExpectationFailedException && $e->getComparisonFailure()) {
                return [
                    'expected' => $e->getComparisonFailure()->getExpected(),
                    'actual' => $e->getComparisonFailure()->getActual()
                ];
            }
        }
        return [
            'expected' => '',
            'actual' => ''
        ];
    }
}
