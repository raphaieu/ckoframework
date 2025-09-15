<?php

require_once __DIR__ . '/vendor/autoload.php';

use CkoFramework\AI\Tests\IntegrationTest;

echo "🚀 Starting AI-Kit Integration Test...\n\n";

try {
    $test = new IntegrationTest();
    $results = $test->runAllTests();
    $test->printResults($results);
    
    // Count results
    $totalTests = 0;
    $passedTests = 0;
    $failedTests = 0;
    $errorTests = 0;
    
    foreach ($results as $category => $tests) {
        if (is_array($tests) && isset($tests['status'])) {
            // Single test result
            $totalTests++;
            switch ($tests['status']) {
                case 'PASS':
                    $passedTests++;
                    break;
                case 'FAIL':
                    $failedTests++;
                    break;
                case 'ERROR':
                    $errorTests++;
                    break;
            }
        } elseif (is_array($tests)) {
            // Multiple test results
            foreach ($tests as $testName => $result) {
                if (is_array($result) && isset($result['status'])) {
                    $totalTests++;
                    switch ($result['status']) {
                        case 'PASS':
                            $passedTests++;
                            break;
                        case 'FAIL':
                            $failedTests++;
                            break;
                        case 'ERROR':
                            $errorTests++;
                            break;
                    }
                }
            }
        }
    }
    
    echo "📊 Test Summary:\n";
    echo "   Total Tests: {$totalTests}\n";
    echo "   ✅ Passed: {$passedTests}\n";
    echo "   ❌ Failed: {$failedTests}\n";
    echo "   ⚠️  Errors: {$errorTests}\n";
    
    if ($failedTests === 0 && $errorTests === 0) {
        echo "\n🎉 All tests passed! AI-Kit is working correctly.\n";
        exit(0);
    } else {
        echo "\n⚠️  Some tests failed. Please check the results above.\n";
        exit(1);
    }
    
} catch (\Exception $e) {
    echo "❌ Test execution failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
